<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as IlluminateBelongsToMany;
use Riesjart\Relaquent\Relations\Concerns\BelongsToManyTrait;
use Riesjart\Relaquent\Relations\Contracts\JoinsRelationsContract;

class BelongsToMany extends IlluminateBelongsToMany implements JoinsRelationsContract
{
    use BelongsToManyTrait;


    // =======================================================================//
    //          Converters
    // =======================================================================//

    /**
     * @param bool $fresh
     *
     * @return HasOneThrough
     */
    public function toHasOneThrough(bool $fresh = false): HasOneThrough
    {
        $query = $fresh ? $this->related->newQuery() : clone $this->query;

        $relation = new HasOneThrough($query, $this->parent, $this->table,
            $this->foreignPivotKey, $this->relatedPivotKey, $this->parentKey, $this->relatedKey, $this->relationName);

        return $relation->withPivot($this->pivotColumns)
            ->using($this->using);
    }

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

        $table = implode(' as ', array_unique([$this->getTable(), $pivotAlias]));
        $one = $this->getQualifiedParentKeyName();
        $two = $pivotAlias . '.' . $this->foreignPivotKey;

        $query->join($table, $one, '=', $two, $type, $where);

        $table = implode(' as ', array_unique([$relatedTable, $alias]));
        $one = $pivotAlias . '.' . $this->relatedPivotKey;
        $two = $alias . '.' . $this->relatedKey;

        $query->join($table, $one, '=', $two, $type, $where);

        return $this;
    }
}
