<?php

namespace Riesjart\Relaquent\Model\Concerns;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Riesjart\Relaquent\QueryBuilder\EloquentQueryBuilder;

/**
 * Synced with illuminate/database v5.5.17
 */
trait UsesRelaquentQueryBuilder
{
    // =======================================================================//
    //          Overrides
    // =======================================================================//

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param QueryBuilder $query
     *
     * @return EloquentQueryBuilder
     */
    public function newEloquentBuilder($query): EloquentQueryBuilder
    {
        return new EloquentQueryBuilder($query);
    }
}
