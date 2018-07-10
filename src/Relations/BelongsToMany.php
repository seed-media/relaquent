<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Builder as ElquentQueryBuilder;
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
     * @return HasOneThrough
     */
    public function toHasOneThrough(): HasOneThrough
    {
        $relation = new HasOneThrough($this->getRelated()->newQuery(), $this->getParent(), $this->getTable(),
            $this->foreignPivotKey, $this->relatedPivotKey, $this->parentKey, $this->relatedKey, $this->getRelationName());

        return $relation
            ->withPivot($this->pivotColumns)
            ->using($this->using);
    }

    // =======================================================================//
    //          Join
    // =======================================================================//

    /**
     * {@inheritdoc}
     */
    public function addAsJoin(ElquentQueryBuilder $query, string $alias = null, string $type = 'inner',
                              bool $where = false): JoinsRelationsContract
    {
        $related = $this->getRelated();
        $relatedTable = $related->getTable();

        $alias = $alias ?: $relatedTable;
        $pivotAlias = $alias . '_pivot';

        $table = $this->getTable() . ' as ' . $pivotAlias;
        $one = $this->getQualifiedParentKeyName();
        $two = $pivotAlias . '.' . $this->foreignPivotKey;

        $query->join($table, $one, '=', $two, $type, $where);

        $table = $relatedTable . ' as ' . $alias;
        $one = $pivotAlias . '.' . $this->relatedPivotKey;
        $two = $alias . '.' . $this->relatedKey;

        $query->join($table, $one, '=', $two, $type, $where);

        return $this;
    }
}
