<?php

namespace Riesjart\Relaquent\Model\Concerns;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Riesjart\Relaquent\QueryBuilder\EloquentQueryBuilder;

trait UsesRelaquentQueryBuilder
{
    // =======================================================================//
    //          Overrides
    // =======================================================================//

    /**
     * @param QueryBuilder $query
     *
     * @return EloquentQueryBuilder
     */
    public function newEloquentBuilder($query): EloquentQueryBuilder
    {
        return new EloquentQueryBuilder($query);
    }
}
