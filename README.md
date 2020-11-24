# Laravel Taster

If you're cooking new features for your Laravel app or just wanna know what tastes better this package is definitely for you!

## First steps
1. Install the package\
`composer require ntpages/laravel-taster`
2. Register service provider\
`Ntpages\LaravelTaster\Provider::class` in the `config/app.php`
2. Run the migrations\
`php artisan migrate`
3. Publish package files\
` php artisan vendor:publish`

## Usage
The package provides multiple scenarios of usage, all of them can be combined on same page and can be measured by different
type of interactions. You should define your Variants, Experiments and Interactions in the database
for consistency and tracking purposes. The package uses tables prefixed with `tsr_`.

### Interactions
In order to be able to measure the influence of the feature or tests you should create interactions.
Table `tsr_interactions`.

#### Events
You can capture few frontend events:
- mouseover
- click
- view (provided by the package, triggered whenever element is in the viewport)

In this case we've prepared a view helper that should be used inside of the attribute definition are of an HTML element.
```blade
<button {{ tsrEvent('wanted-discount', 'mouseover') }}>
    Get 50% discount
</button>
```

You can also specify multiple events. Have in mind that all of them have the same "repetition" policy and are related
to the same interaction type.
```blade
<button {{ tsrEvent('wanted-discount', ['mouseover', 'click']) }}>
    Get 50% discount
</button>
```

If you need to split those to different interactions just wrap the target with multiple html tags.
```blade
<button {{ tsrEvent('requested-discount', 'click') }}>
    {{-- takes all the space in the button --}}
    <div {{ tsrEvent('wanted-discount', 'mouseover') }}>
        Get 50% discount
    </div>
</button>
```

It's also possible to capture events all the times that those happen, for that just provide a third parameter.
```blade
<button {{ tsrEvent('number-is-low', 'click', false) }}>
    Increase number
</button>
```
> The `view` event ignores the third element

#### Backend
You always can send an event manually whenever you need if for example you've created a route for each variant/feature,
and you want to track the visits to those pages, just use this structure in your controller:
```php
use Ntpages\LaravelTaster\Models\Interaction;
use Ntpages\LaravelTaster\Events\Interact;
use Ntpages\LaravelTaster\Models\Variant;

event(
    new Interact(
        Interaction::findByKey('visit-page'),
        Variant::findByKey('home-update')
    )
);
```

You also can auto detect which variant user saw.
```php
use Ntpages\LaravelTaster\Models\Experiment;

app('taster')->interact('interaction-key', Experiment::findByKey('experiment-key'));
```

Or for feature flagging:
```php
use Ntpages\LaravelTaster\Models\Variant;

app('taster')->interact('interaction-key', Variant::findByKey('feature-key'));
```


### Feature flagging
Stored under `tsr_variants` and not related to any experiment. Managed simply modifying the `portion` attribute,
set it to `0` if you want to disable the feature or any number between `0` and `0.1` to indicate the percentage of
audience that should see that feature.
```blade
@feature('feature-key')
    feature body here
@fallback
    optional fallback
@endfeature
```

### A/B testing
Stored under `tsr_variants` and grouped by `experiment_id` from the `tsr_experiments` table.
```blade
@experiment('experiment-key')

    @variant('variant-key-1')
        <a href="/pet-owner.html">How to have a pet</a>
    @endvariant

    @variant('variant-key-2')
        <a href="/funny-cats.html">This cats are smashing the day</a>
    @endvariant

    @variant('variant-key-3')
        <a href="/lovely-dogs.html">Dogs are better than humans</a>
    @endvariant

@endexperiment
```
