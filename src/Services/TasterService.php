<?php

namespace Ntpages\LaravelTaster\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

use Ntpages\LaravelTaster\Events\Impression;
use Ntpages\LaravelTaster\Exceptions\UnexpectedInteractionException;
use Ntpages\LaravelTaster\Exceptions\UnexpectedVariantException;
use Ntpages\LaravelTaster\Exceptions\WrongPortioningException;
use Ntpages\LaravelTaster\Exceptions\ElementNotFoundException;
use Ntpages\LaravelTaster\Exceptions\AbstractTasterException;
use Ntpages\LaravelTaster\Models\Interaction;
use Ntpages\LaravelTaster\Models\Experiment;
use Ntpages\LaravelTaster\Events\Interact;
use Ntpages\LaravelTaster\Models\Variant;

class TasterService
{
    const JS_EVENTS = ['view', 'hover', 'click'];

    /**
     * @var string
     */
    private string $cookieKey;

    /**
     * @var int
     */
    private int $cookieTtl;

    /**
     * @var array
     */
    private array $cookies;

    /**
     * @var Collection[]
     */
    private array $instances;

    private ?Experiment $currentExperiment;
    private ?Variant $currentVariant;

    public function __construct()
    {
        $this->cookieKey = config('taster.cookie.key', 'taster');
        $this->cookieTtl = config('taster.cookie.ttl', 2628000);
        $this->cookies = json_decode(Cookie::get($this->cookieKey, '{}'), true) ?? [];

        if (!array_key_exists('uuid', $this->cookies)) {
            $this->cookies['uuid'] = Str::uuid()->toString();
        }

        // setting up the cache for the PHP app
        $cache = config('taster.cache', ['key' => 'taster', 'ttl' => null]);
        $args = [$cache['key']];

        // when cache ttl is null it's stored forever
        if ($cache['ttl'] && $cache['ttl'] > 0) {
            array_push($args, $cache['ttl']);
        }

        // the callback that retrieves the models
        array_push($args, function () {
            foreach ($experiments = Experiment::with('variants')->get() as $experiment) {
                $experiment->variants = $experiment->variants->keyBy('key');
            }

            return [
                Interaction::class => Interaction::all()->keyBy('key'),
                Experiment::class => $experiments->keyBy('key'),
            ];
        });

        // toggling between two different cache strategies
        $this->instances = count($args) === 3 ? Cache::remember(...$args) : Cache::forever(...$args);
    }

    /**
     * @param string $key
     * @return string
     * @throws UnexpectedInteractionException
     * @throws ElementNotFoundException
     */
    public function getInteractionUrl(string $key): ?string
    {
        if (is_null($this->currentVariant)) {
            throw new UnexpectedInteractionException();
        }

        $interaction = $this->instances[Interaction::class]->get($key);

        if (is_null($interaction)) {
            throw new ElementNotFoundException(Interaction::class, $key);
        }

        return route(config('taster.route.name'), [
            // protecting from guessing the ids combination
            'token' => encrypt([
                'variant' => $this->currentVariant->id,
                'interaction' => $interaction->id
            ])
        ]);
    }

    /**
     * @param string $key Unique name of the experiment
     * @return int Picked variant ID
     * @throws ElementNotFoundException
     * @throws WrongPortioningException
     */
    public function experiment(string $key): ?int
    {
        $this->currentExperiment = $this->instances[Experiment::class]->get($key);
        $this->currentVariant = null;

        if (is_null($this->currentExperiment)) {
            throw new ElementNotFoundException(Experiment::class, $key);
        }

        // see if already have a variant assigned
        $cookieValue = $this->cookies[$this->currentExperiment->id] ?? null;

        if (is_int($cookieValue)) {
            return $cookieValue;
        }

        // retrieving the variant
        $pickedId = $this->pick($this->currentExperiment->variants->pluck('portion', 'id')->toArray());

        // first time triggering this experiment
        Impression::dispatch($this->currentExperiment->variants->find($pickedId), $this->getUuid(), request()->fullUrl());

        // saving the variant for the user
        $this->cookies[$this->currentExperiment->id] = $pickedId;

        Cookie::queue($this->cookieKey, json_encode($this->cookies), $this->cookieTtl);

        return $pickedId;
    }

    /**
     * @param string $key Unique name of the variant for the current experiment
     * @return int Variant id
     * @throws UnexpectedVariantException
     * @throws ElementNotFoundException
     */
    public function variant(string $key): ?int
    {
        if (is_null($this->currentExperiment)) {
            throw new UnexpectedVariantException($key);
        }

        $this->currentVariant = $this->currentExperiment->variants->get($key);

        if (is_null($this->currentVariant)) {
            throw new ElementNotFoundException(Variant::class, $key);
        }

        return $this->currentVariant->id;
    }

    /**
     * @param string $key Unique name of the interaction
     * @throws UnexpectedInteractionException
     * @throws ElementNotFoundException
     */
    public function interact(string $key): void
    {
        if (is_null($this->currentVariant)) {
            throw new UnexpectedInteractionException();
        }

        $interaction = $this->instances[Interaction::class]->get($key);

        if (is_null($interaction)) {
            throw new ElementNotFoundException(Interaction::class, $key);
        }

        Interact::dispatch($interaction, $this->currentVariant, $this->cookies['uuid'], request()->url());
    }

    /**
     * @param string $experimentKey The experiment key
     * @param array|string $payload This argument defines how the method works. In case you pass an array with
     * `string:variantKey => string|array:interactions` it'll lunch interactions for corresponding variants.
     *  When you pass a string it'll be an interaction key that will be lunched on the current selected variant.
     *
     * @throws AbstractTasterException
     */
    public function record(string $experimentKey, $payload)
    {
        $pickedId = $this->experiment($experimentKey);
        $interactions = [];

        if (is_array($payload)) {
            foreach ($payload as $vKey => $iKeys) {
                if ($pickedId === $this->variant($vKey)) {
                    $interactions = is_array($iKeys) ? $iKeys : [$iKeys];
                    break;
                }
            }
        } else {
            foreach ($this->currentExperiment->variants as $variant) {
                if ($pickedId === $this->variant($variant->key)) {
                    $interactions = [$payload];
                    break;
                }
            }
        }

        foreach ($interactions as $interaction) {
            $this->interact($interaction);
        }
    }

    /**
     * @return string The unique identifier of the current visitor used to store the interactions
     */
    public function getUuid(): string
    {
        return $this->cookies['uuid'];
    }

    /**
     * Uses the portion for create percentage probability of random selection
     * @param array $items
     * @return int
     * @throws WrongPortioningException
     */
    private function pick(array $items): ?int
    {
        if (array_sum(array_values($items)) > 1) {
            throw new WrongPortioningException(1);
        }

        $ids = [];
        foreach ($items as $id => $portion) {
            $i = $portion * 100;
            while (--$i > -1) {
                array_push($ids, $id);
            }
        }

        return $ids[array_rand($ids, 1)];
    }
}
