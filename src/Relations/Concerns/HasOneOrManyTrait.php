<?php

namespace Riesjart\Relaquent\Relations\Concerns;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Riesjart\Relaquent\Relations\Contracts\JoinsRelationsContract;

trait HasOneOrManyTrait
{
    // =======================================================================//
    //          Join
    // =======================================================================//

    /**
     * @param EloquentQueryBuilder $query
     * @param string|null $alias
     * @param string $type
     * @param bool $where
     *
     * @return $this
     */
    public function addAsJoin(EloquentQueryBuilder $query, string $alias = null, string $type = 'inner',
                              bool $where = false): JoinsRelationsContract
    {
        $relatedTable = $this->related->getTable();

        $alias = $alias ?: $relatedTable;

        $table = implode(' as ', array_unique([$relatedTable, $alias]));
        $one = $this->getQualifiedParentKeyName();
        $two = $alias . '.' . $this->getForeignKeyName();

        $query->join($table, $one, '=', $two, $type, $where);

        return $this;
    }
}
