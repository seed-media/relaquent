<?php

namespace Riesjart\Relaquent\Relations\Contracts;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;

interface JoinsRelationsContract
{
    /**
     * @param EloquentQueryBuilder $query
     * @param string|null $alias
     * @param string $type
     * @param bool $where
     *
     * @return $this
     */
    public function addAsJoin(EloquentQueryBuilder $query, string $alias = null, string $type = 'inner',
                              bool $where = false): JoinsRelationsContract;
}
