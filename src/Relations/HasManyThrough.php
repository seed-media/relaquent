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
     * {@inheritdoc}
     */
    public function addAsJoin(EloquentQueryBuilder $query, string $alias = null, string $type = 'inner',
                              bool $where = false): JoinsRelationsContract
    {
        $related = $this->getRelated();
        $relatedTable = $related->getTable();

        $alias = $alias ?: $relatedTable;
        $pivotAlias = $alias . '_pivot';

        $parent = $this->getParent();

        $table = $parent->getTable() . ' as ' . $pivotAlias;
        $one = $query->getModel()->getTable() . '.' . $this->localKey;
        $two = $pivotAlias . '.' . $this->firstKey;

        $query->join($table, $one, '=', $two, $type, $where);

        $table = $relatedTable . ' as ' . $alias;
        $one = $pivotAlias . '.' . $this->secondLocalKey;
        $two = $alias . '.' . $this->secondKey;

        $query->join($table, $one, '=', $two, $type, $where);

        return $this;
    }
}
