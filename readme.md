## Version

Current release: **v0.1.1**

This repository uses [Semantic Versioning (SemVer) v2.0.0](http://semver.org/spec/v2.0.0.html).

## Requirements

* PHP >= 7.1
* Laravel >= 5.6

## Installation

Pull this package in through Composer.

```
composer require riesjart/relaquent "^0.1"
```

There is no service provider that needs to be registered in your Laravel application.

## Features

### Additional relationship types

* `BelongsToMorph`
* `HasOneThrough`
* `MorphOneThrough`

### Making query joins based on relations

* `BelongsTo`
* `BelongsToMany`
* `HasMany`
* `HasManyThrough`
* `HasOne`

### Use models as (morph) pivots

### Convert relationships to other types

* `MorphTo` to `BelongsToMorph`
* `MorphMany` to `MorphOne`
* `HasMany` to `HasOne`
* `MorphToMany` to `HasMany`
* `MorphToMany` to `MorphMany`
* `MorphToMany` to `MorphOneThrough`
* `BelongsToMany` to `HasMany`
* `BelongsToMany` to `HasOneThrough`
* `BelongsTo` to "self-referring" `HasMany`
* `BelongsTo` to "self-referring-without-self" `HasMany`

### Relation helpers

#### BelongsTo / MorphTo / BelongsToMorph

`is`, `isDirty`, `isNot`, `isNull`, `getForeignValue`, `notNull`

`MorphTo` only: `getMorphTypeValue`, `isOfType`

#### BelongsToMany / MorphToMany / HasOneThrough / MorphOneThrough

`make`, `withAllPivotColumns`

## Future plans

### Additional relationship types

* `BelongsToThrough`

### Making query joins based on relations

* `BelongsToMorph`
* `HasOneThrough`
* `MorphMany`
* `MorphOne`
* `MorphOneThrough`
* `MorphTo`
* `MorphToMany`
