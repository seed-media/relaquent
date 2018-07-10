<?php

namespace Riesjart\Relaquent\QueryBuilder\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

/**
 * Synced with illuminate/database v5.5.17
 */
trait EagerLoadsPivotRelation
{
    // =======================================================================//
    //          Overrides
    // =======================================================================//

    /**
     * Eagerly load the relationship on a set of models.
     *
     * @param array $models
     * @param string $name
     * @param Closure $constraints
     *
     * @return array
     */
    protected function eagerLoadRelation(array $models, string $name, Closure $constraints): array
    {
        if ($name === 'pivot') {

            $pivots = EloquentCollection::make($models)->pluck('pivot')->filter();

            if ($pivots->count() === count($models)) {

                EloquentCollection::make($pivots)->load($this->relationsNestedUnder('pivot'));

                return $models;
            }
        }

        return parent::eagerLoadRelation($models, $name, $constraints);
    }
}
