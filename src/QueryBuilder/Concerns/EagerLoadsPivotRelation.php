<?php

namespace Riesjart\Relaquent\QueryBuilder\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

trait EagerLoadsPivotRelation
{
    // =======================================================================//
    //          Overrides
    // =======================================================================//

    /**
     * @param array $models
     * @param string $name
     * @param Closure $constraints
     *
     * @return array
     */
    protected function eagerLoadRelation(array $models, $name, Closure $constraints): array
    {
        if ($name === 'pivot') {

            $pivots = EloquentCollection::make($models)
                ->pluck('pivot')
                ->filter();

            if ($pivots->count() === count($models)) {

                EloquentCollection::make($pivots)
                    ->load($this->relationsNestedUnder('pivot'));

                return $models;
            }
        }

        return parent::eagerLoadRelation($models, $name, $constraints);
    }
}
