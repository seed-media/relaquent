<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Relations\HasManyThrough as IlluminateHasManyThrough;
use Riesjart\Relaquent\Relations\Contracts\JoinsRelationsContract;

class HasManyThrough extends IlluminateHasManyThrough implements JoinsRelationsContract
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
        $pivotAlias = $alias . '_pivot';

        $table = implode(' as ', array_unique([$this->parent->getTable(), $pivotAlias]));
        $one = $query->qualifyColumn($this->localKey);
        $two = $pivotAlias . '.' . $this->firstKey;

        $query->join($table, $one, '=', $two, $type, $where);

        $table = implode(' as ', array_unique([$relatedTable, $alias]));
        $one = $pivotAlias . '.' . $this->secondLocalKey;
        $two = $alias . '.' . $this->secondKey;

        $query->join($table, $one, '=', $two, $type, $where);

        return $this;
    }
}
