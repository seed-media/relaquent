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
     * @return string
     */
    public function getPivotColumnsCacheKey(): string
    {
        return sprintf('db.columns.%s.%s', $this->query->getConnection()->getName(), $this->table);
    }

    /**
     * @return string
     */
    public function guessPivotClass(): string
    {
        $namespace = (new ReflectionObject($this->getParent()))->getNamespaceName();

        return $namespace . '\\' . Str::studly(Str::singular($this->table));
    }

    /**
     * @param array $attributes
     *
     * @return Model
     */
    public function make(array $attributes = []): Model
    {
        return $this->related->newInstance($attributes);
    }

    /**
     * @return $this
     */
    public function withAllPivotColumns(): BelongsToMany
    {
        $columns = Container::getInstance()
            ->make('cache')
            ->rememberForever($this->getPivotColumnsCacheKey(), function (): array {

                return $this->query->getConnection()
                    ->getSchemaBuilder()
                    ->getColumnListing($this->table);
            });

        return $this->withPivot($columns);
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
            $pivot->newQuery(), $this->parent, $pivot->qualifyColumn($this->foreignPivotKey), $this->parentKey
        );
    }
}
