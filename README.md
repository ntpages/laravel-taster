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

#### Impressions
That's the only time you'll need the blade directive, it's so simple as this:
```blade
@interact('interaction-key')
```

#### Events
In this case we've prepared a view helper that should be used inside of the attribute definition are of an HTML element.
```blade
<button {{ interact('interaction-key', 'js-event-key') }}>Get 50% discount</button>
```
> The package supports only one event per element so if you need to capture multiple, like for example `mouseenter`
> and `mouseleave` you can wrap elements one inside another
```blade
{{-- event 1 --}}
<button {{ interact('sign-in-cta', 'click') }}>

    {{-- event 2 --}}
    <div {{ interact('wanted-to-sign-in', 'mouseenter') }}>

        {{-- event 3 --}}
        <div {{ interact('reconsidered-sign-in', 'mouseleave') }}>
            Get 50% discount
        </div>

    </div>

</button>
```

#### Backend
You always can send an event manually whenever you need if for example you've created a route for each variant/feature,
and you want to track the visits to those pages, just use this structure in your controller:
```php
use Ntpages\LaravelTaster\Models\Interaction;
use Ntpages\LaravelTaster\Models\Variant;
use Ntpages\LaravelTaster\Events\Interact;

$interaction = Interaction::where('key', 'visit')->first();
$variant = Variant::where('key', 'cat-page')->first();

if ($interaction && $variant) {
    event(new Interact($interaction, $variant));
}
```

> Keep in mind that interactions should always be used inside of `@variant` or `@feature` however, you'll see the
> `UnexpectedInteractionException` in case you forgot how that works.

### Feature flagging
Stored under `tsr_variants` and not related to any experiment. Managed simply modifying the `portion` attribute,
set it to `0` if you want to disable the feature or any number between `0` and `0.1` to indicate the percentage of
audience that should see that feature.
```blade
@feature('feature-key')
    @interact('rendered')
    feature body here
@else
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
