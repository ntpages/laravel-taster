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

### Blade directives and helpers

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

todo: explain how javascript events work

### From within PHP

todo: explain how it can be used manually
