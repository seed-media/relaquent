<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot as IlluminatePivot;

class Pivot extends IlluminatePivot
{
    // =======================================================================//
    //          Overrides
    // =======================================================================//

    /**
     * @param Model $parent
     * @param array $attributes
     * @param string $table
     * @param bool $exists
     *
     * @return static
     */
    public static function fromAttributes(Model $parent, $attributes, $table, $exists = false): Pivot
    {
        $instance = new static;

        $instance->setConnection($parent->getConnectionName())
            ->setTable($table)
            ->setRawAttributes($attributes, true);

        $instance->pivotParent = $parent;

        $instance->exists = $exists;

        $instance->timestamps = $instance->hasTimestampAttributes();

        return $instance;
    }

    /**
     * @return string
     */
    public function getCreatedAtColumn(): string
    {
        return $this->pivotParent
            ? parent::getCreatedAtColumn()
            : Model::getCreatedAtColumn();
    }

    /**
     * @return string
     */
    public function getForeignKey(): string
    {
        return $this->pivotParent
            ? parent::getForeignKey()
            : Model::getForeignKey();
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->pivotParent
            ? parent::getTable()
            : Model::getTable();
    }

    /**
     * @return string
     */
    public function getUpdatedAtColumn(): string
    {
        return $this->pivotParent
            ? parent::getUpdatedAtColumn()
            : Model::getUpdatedAtColumn();
    }
}
