# Laravel Taster

If you're cooking new features for your Laravel app or just want to know what tastes better this package is definitely
for you!

## First steps

1. Install the package\
   `composer require ntpages/laravel-taster`
2. Register service provider\
   `Ntpages\LaravelTaster\Provider::class` in the `config/app.php`
2. Run the migrations\
   `php artisan migrate`
3. Publish package files\
   ` php artisan vendor:publish`

## Structure

The logic of the package is based on three models, Experiment, Variant and Interaction. All of them are stored in the
database on tables with `tsr_` prefix. All three have a unique key and name in order to be easily identifiable in the
code or user interface.

### Experiment

It is a unit that groups different types of variants to easily split traffic and statistics between them. Variants
hardly depends on it, which means if you are building a UI where you can create/delete experiments deleting one would
clear all related data.

### Variant

It's an instance that represents one way to seeing the experiment. There's one important thing to be aware of, and it's
the `portion` attribute. It represents the approximate percentage of visitors that should reach the variant. So you can
have as many variants as you need in one experiment as long as their portions sum 1, the total amount of visitors.

In order to not lose any data about experiments when there's available portion a default variant is created.

#### Interactions

The simplest unit that helps to keep track and compare the performance of the experiment variants.

## Usage

In this section you'll see different approaches that can be used with this package.

### Build in helpers

This package provides a set of functions used to config & track your experiments.

#### Front-end

This is the most common way of usage. Using the next structure you can be sure it'll work as expected you should only
know the keys of the currently enabled experiments and their variants.

```blade
@experiment('pet-preferences')

    @variant('no-pet')
        <a href="/get-a-pet.html">How to have a pet</a>
    @endvariant

    @variant('cat-lover')
        <a href="/funny-cats.html">This cats are smashing the day</a>
    @endvariant

    @variant('dog-lover')
        <a href="/lovely-dogs.html">Dogs are better than humans</a>
    @endvariant

@endexperiment
```

**How to track interactions?**\
It's as simple as just adding needed attributes to the element you want to track and including the javascript assets.\

> IMPORTANT: these helpers can only be used inside of `@variant` blade directive.

When using the `tsrAttrs` helper you'll need to add the package javascripts. You can do that however you want, but the
recommended way is deferring the script loading.

```blade
<script defer async src="{{ asset('vendor/tsr/taster.js') }}"></script>
```

Another important thing about `tsrAttrs` helper is that you'll always need a html elements so if you don't have one just
create an empty div. Have in mind that the only event that will make sense in that case is `view`.

```blade
@experiment('expertiment-1')

   {{-- other variants --}}

    @variant('variant-1')
        <a href="/page.html" {{ tsrAttrs('click-intent', 'hover') }}>Page</a>
    @endvariant

@endexperiment
```

> There are some special cases in the html, when the tag has it's own actions executed by the browser. For that just use an extra wrapper.

```blade
<a href="some-page.html">
   <span {{ tsrAttrs('click-page', 'click') }}>
      Login
   </span>
</a>
```

The package also provides the possibility of generation of the interaction URL in case you need it somewhere else.

```blade
@experiment('expertiment-1')

   {{-- other variants --}}

    @variant('variant-1')
        <a href="/page.html">Page</a>
        <script>
            const interactionUrl = '{{ tsrUrl('interaction-key') }}';
            // do something with that URL
        </script>
    @endvariant

@endexperiment
```

### From within PHP

There are sometimes when you can capture interactions on back-end. It's always a good idea as that way you're not
overloading the front-end with extra javascript from the package. For that you can access to `TasterService` right
inside of Laravel application by using the `app()` helper or using a dependency injection technique.

```php
use Illuminate\Support\Facades\Log;

use Ntpages\LaravelTaster\Exceptions\AbstractTasterException;
use Ntpages\LaravelTaster\Services\TasterService;

class FooController
{
    public function barAction(TasterService $taster)
    {
        try {
            $taster->record('experiment-key', [
                'variant-1-key' => 'interaction-1-key',
                'variant-2-key' => [
                    'interaction-1-key',
                    'interaction-2-key'
                ]
            ]);
        } catch (AbstractTasterException $e) {
            Log::error('Taster says: ' . $e->getMessage());
        }

        return view('foo.bar');
    }
}
```
