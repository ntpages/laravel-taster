<?php

namespace Ntpages\LaravelTaster\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;

use Ntpages\LaravelTaster\Exceptions\AbstractTasterException;
use Ntpages\LaravelTaster\Exceptions\UnexpectedInteractionException;
use Ntpages\LaravelTaster\Exceptions\UnexpectedVariantException;
use Ntpages\LaravelTaster\Exceptions\WrongPortioningException;
use Ntpages\LaravelTaster\Exceptions\ElementNotFoundException;
use Ntpages\LaravelTaster\Models\Interaction;
use Ntpages\LaravelTaster\Models\Experiment;
use Ntpages\LaravelTaster\Models\Variant;
use Ntpages\LaravelTaster\Events\Interact;

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
        $this->cookies = json_decode(Cookie::get($this->cookieKey, '{}'), true);

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
    public function getInteractionUrl(string $key): string
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
        $cookieValue = $this->getCookie($this->currentExperiment->id);

        if (is_int($cookieValue)) {
            return $cookieValue;
        }

        // retrieving the variant
        $pickedId = $this->pick($this->currentExperiment->variants->pluck('portion', 'id')->toArray());

        // saving it
        $this->setCookie($this->currentExperiment->id, $pickedId);

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

        Interact::dispatch($interaction, $this->currentVariant, request()->url());
    }

    /**
     * @param string $experimentKey The experiment key
     * @param array|string $data Associated array with `variantKey` => `interactionKey|interactionKeys`
     * @throws AbstractTasterException
     */
    public function record(string $experimentKey, array $data)
    {
        $pickedId = $this->experiment($experimentKey);

        foreach ($data as $key => $interactions) {
            if ($pickedId === $this->variant($key)) {
                if (!is_array($interactions)) {
                    $interactions = [$interactions];
                }
                foreach ($interactions as $interaction) {
                    $this->interact($interaction);
                }
                return;
            }
        }
    }

    /**
     * Uses the portion for create percentage probability of random selection
     * @param array $items
     * @return mixed
     * @throws WrongPortioningException
     */
    private function pick(array $items)
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

    /**
     * @param int $experimentId
     * @param int $variantId
     */
    private function setCookie(int $experimentId, int $variantId)
    {
        $this->cookies[$experimentId] = $variantId;

        Cookie::queue($this->cookieKey, json_encode($this->cookies), $this->cookieTtl);
    }

    /**
     * @param int $experimentId
     * @return string|null
     */
    private function getCookie(int $experimentId)
    {
        return $this->cookies[$experimentId] ?? null;
    }
}
