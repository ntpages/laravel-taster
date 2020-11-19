<?php

namespace Ntpages\LaravelTaster\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;

use Ntpages\LaravelTaster\Exceptions\UnexpectedInteractionException;
use Ntpages\LaravelTaster\Exceptions\InteractionNotFoundException;
use Ntpages\LaravelTaster\Exceptions\ExperimentNotFoundException;
use Ntpages\LaravelTaster\Exceptions\UnexpectedVariantException;
use Ntpages\LaravelTaster\Exceptions\FeatureNotFoundException;
use Ntpages\LaravelTaster\Exceptions\VariantNotFoundException;
use Ntpages\LaravelTaster\Exceptions\WrongPortioningException;
use Ntpages\LaravelTaster\Models\Interaction;
use Ntpages\LaravelTaster\Models\Experiment;
use Ntpages\LaravelTaster\Models\Variant;

class TasterService
{
    const EVENT_TYPE_MOUSEOVER = 'mouseover';
    const EVENT_TYPE_CLICK = 'click';
    const EVENT_TYPE_VIEW = 'view';

    // just semantics
    const EVENT_TYPES = [
        self::EVENT_TYPE_MOUSEOVER,
        self::EVENT_TYPE_CLICK,
        self::EVENT_TYPE_VIEW,
    ];

    // to keep code safer
    const COOKIE_KEYS = [
        Experiment::class => 'ex',  // when saving experiment
        Variant::class => 'fe'      // when saving feature
    ];

    /**
     * @var Collection|Interaction[]
     */
    private $interactions;

    /**
     * @var Collection|Experiment[]
     */
    private $experiments;

    /**
     * @var Collection|Variant[]
     */
    private $features;

    /**
     * @var Experiment
     */
    private $currentExperiment;

    /**
     * @var Variant
     */
    private $currentVariant;

    /**
     * @var string
     */
    private $cookieKey;

    /**
     * @var int
     */
    private $cookieTtl;

    /**
     * @see TasterService::COOKIE_KEYS
     * @var array[string][int|bool]
     */
    private $cookies;

    /**
     * TasterService constructor.
     */
    public function __construct()
    {
        $this->cookieKey = config('taster.cookie.key', 'tsr');
        $this->cookieTtl = config('taster.cookie.ttl', 2628000);

        // getting current cookies
        $this->cookies = Cookie::has($this->cookieKey)
            ? json_decode(Cookie::get($this->cookieKey), true)
            : array_fill_keys(array_values(self::COOKIE_KEYS), []);

        $retrieve = function () {
            return [
                Interaction::all()->keyBy('key'),
                Experiment::with('variants')->get()->keyBy('key'),
                Variant::features()->get()->keyBy('key')
            ];
        };

        $cacheConfig = config('taster.cache');
        list(
            $this->interactions,
            $this->experiments,
            $this->features
            ) =
            $cacheConfig['ttl'] && $cacheConfig['key']
                ? Cache::remember($cacheConfig['key'], $cacheConfig['ttl'], $retrieve)
                : $retrieve();
    }

    /**
     * @param string $key
     * @return mixed
     * @throws InteractionNotFoundException
     */
    public function getInteraction(string $key)
    {
        if (!$this->interactions->has($key)) {
            throw new InteractionNotFoundException($key);
        }

        return $this->interactions->get($key);
    }

    /**
     * @return Variant
     * @throws UnexpectedInteractionException
     */
    public function getCurrentVariant()
    {
        if (!$this->currentVariant) {
            throw new UnexpectedInteractionException();
        }

        return $this->currentVariant;
    }

    /**
     * @param string $key
     * @return int
     * @throws ExperimentNotFoundException
     * @throws WrongPortioningException
     */
    public function experiment(string $key)
    {
        $this->currentExperiment = $this->experiments->get($key);
        $this->currentVariant = null;

        if (!$this->currentExperiment) {
            throw new ExperimentNotFoundException($key);
        }

        $cookieValue = $this->getCookie($this->currentExperiment);

        if (!is_null($cookieValue)) {
            return $cookieValue;
        }

        // retrieving the variant
        $variantId = $this->pickValue($this->currentExperiment->variants->pluck('portion', 'id')->toArray());

        // saving it
        $this->setCookie($variantId, $this->currentExperiment);

        return $variantId;
    }

    /**
     * @param string $key
     * @return int
     * @throws VariantNotFoundException
     * @throws UnexpectedVariantException
     */
    public function variant(string $key)
    {
        if (!$this->currentExperiment) {
            // fixme: not reachable because of the blade compilation. Unexpected `case:` thrown before hit this point
            throw new UnexpectedVariantException($key);
        }

        $this->currentVariant = $this->currentExperiment->variants->filter(function (Variant $variant) use ($key) {
            return $variant->key === $key;
        })->first();

        if (!$this->currentVariant) {
            throw new VariantNotFoundException($this->currentExperiment->key, $key);
        }

        return $this->currentVariant->id;
    }

    /**
     * @param string $key
     * @return bool
     * @throws FeatureNotFoundException
     * @throws WrongPortioningException
     */
    public function feature(string $key)
    {
        $this->currentVariant = $this->features->get($key);
        $this->currentExperiment = null;

        if (!$this->currentVariant) {
            throw new FeatureNotFoundException($key);
        }

        $cookieValue = $this->getCookie($this->currentVariant);

        if (!is_null($cookieValue)) {
            return $cookieValue;
        }

        $featureState = (bool)$this->pickValue([
            false => 1 - $this->currentVariant->portion,
            true => $this->currentVariant->portion
        ]);

        $this->setCookie($featureState, $this->currentVariant);

        return $featureState;
    }

    /**
     * Uses the portion for create percentage probability of random selection
     * @param array $items
     * @return mixed
     * @throws WrongPortioningException
     */
    private function pickValue(array $items)
    {
        if (array_sum(array_values($items)) > 1) {
            throw new WrongPortioningException();
        }

        $arr = [];
        foreach ($items as $key => $portion) {
            $i = $portion * 100;
            while (--$i > -1) {
                array_push($arr, $key);
            }
        }

        return $arr[array_rand($arr, 1)];
    }

    /**
     * @param int|boolean $value
     * @param Experiment|Variant $object
     */
    private function setCookie($value, $object)
    {
        $this->cookies[self::COOKIE_KEYS[get_class($object)]][$object->id] = $value;

        Cookie::queue($this->cookieKey, json_encode($this->cookies), $this->cookieTtl);
    }

    /**
     * @param Experiment|Variant $object
     * @return int|boolean|null
     */
    private function getCookie($object)
    {
        return $this->cookies[self::COOKIE_KEYS[get_class($object)]][$object->id] ?? null;
    }
}
