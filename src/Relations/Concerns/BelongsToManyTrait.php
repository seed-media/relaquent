<?php

namespace Riesjart\Relaquent\Relations\Concerns;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use ReflectionObject;
use Riesjart\Relaquent\Relations\HasMany;

trait BelongsToManyTrait
{
    /**
     * @param array $attributes
     *
     * @return Model
     */
    public function make(array $attributes = []): Model
    {
        return $this->getRelated()->newInstance($attributes);
    }

    /**
     * @return $this
     */
    public function withAllPivotColumns(): BelongsToMany
    {
        $columns = Container::getInstance()->make('cache')
            ->rememberForever($this->getPivotColumnsCacheKey(), function () {

                return $this->getQuery()->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
            });

        return $this->withPivot($columns);
    }

    /**
     * @return string
     */
    public function getPivotColumnsCacheKey(): string
    {
        return sprintf('db.columns.%s.%s', $this->getQuery()->getConnection()->getName(), $this->getTable());
    }

    /**
     * @return string
     */
    public function guessPivotClass(): string
    {
        $namespace = (new ReflectionObject($this->getParent()))->getNamespaceName();

        return $namespace . '\\' . Str::studly(Str::singular($this->getTable()));
    }

    // =======================================================================//
    //          Converters
    // =======================================================================//

    /**
     * @param string|null $pivotClass
     *
     * @return HasMany
     */
    public function toHasMany(string $pivotClass = null): HasMany
    {
        $pivotClass = $pivotClass ?: $this->guessPivotClass();
        $pivot = new $pivotClass;

        return new HasMany(
            $pivot->newQuery(), $this->getParent(), $pivot->getTable() . '.' . $this->foreignPivotKey, $this->parentKey
        );
    }
}
