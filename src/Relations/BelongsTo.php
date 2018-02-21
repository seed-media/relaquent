<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo as IlluminateBelongsTo;
use Riesjart\Relaquent\Relations\Concerns\BelongsToTrait;
use Riesjart\Relaquent\Relations\Contracts\JoinsRelationsContract;

class BelongsTo extends IlluminateBelongsTo implements JoinsRelationsContract
{
    use BelongsToTrait;


    /**
     * @param Model|int|string $other
     *
     * @return bool
     */
    public function is($other): bool
    {
        if ($other instanceof Model) {

            return $this->getForeignValue() === $other->getAttribute($this->ownerKey) &&
                $this->related->getTable() === $other->getTable() &&
                $this->related->getConnectionName() === $other->getConnectionName();
        }

        return $this->getForeignValue() == $other;
    }

    /**
     * @return bool
     */
    public function isDirty(): bool
    {
        return $this->parent->isDirty($this->foreignKey);
    }

    /**
     * @return bool
     */
    public function isNull(): bool
    {
        return is_null($this->getForeignValue());
    }

    // =======================================================================//
    //          Converters
    // =======================================================================//

    /**
     * @param string|null $foreignKey
     *
     * @return HasMany
     */
    public function toSelfReferring(string $foreignKey = null): HasMany
    {
        $parent = $this->parent;

        $instance = new $parent;

        $foreignKey = $foreignKey ?: $this->foreignKey;

        return new HasMany($instance->newQuery(), $parent, $instance->qualifyColumn($foreignKey), $this->foreignKey);
    }

    /**
     * @param string|null $foreignKey
     *
     * @return HasMany
     */
    public function toSelfReferringWithoutSelf(string $foreignKey = null): HasMany
    {
        $relation = $this->toSelfReferring($foreignKey);

        return $relation->where($relation->getRelated()->getQualifiedKeyName(), '!=', $this->parent->getKey());
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

        $table = implode(' as ', array_unique([$relatedTable, $alias]));
        $one = $this->getQualifiedForeignKey();
        $two = $alias . '.' . $this->ownerKey;

        $query->join($table, $one, '=', $two, $type, $where);

        return $this;
    }
}
