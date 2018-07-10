<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot as IlluminatePivot;

/**
 * Synced with illuminate/database v5.5.17
 */
class Pivot extends IlluminatePivot
{
    // =======================================================================//
    //          Overrides
    // =======================================================================//

    /**
     * {@inheritdoc}
     */
    public static function fromAttributes(Model $parent, $attributes, $table, $exists = false): Pivot
    {
        $instance = new static;

        // The pivot model is a "dynamic" model since we will set the tables dynamically
        // for the instance. This allows it work for any intermediate tables for the
        // many to many relationship that are defined by this developer's classes.
        $instance->setConnection($parent->getConnectionName())
            ->setTable($table)
            ->setRawAttributes($attributes, true);

        // We store off the parent instance so we will access the timestamp column names
        // for the model, since the pivot model timestamps aren't easily configurable
        // from the developer's point of view. We can use the parents to get these.
        $instance->pivotParent = $parent;

        $instance->exists = $exists;

        $instance->timestamps = $instance->hasTimestampAttributes();

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAtColumn(): string
    {
        return $this->pivotParent
            ? parent::getCreatedAtColumn()
            : Model::getCreatedAtColumn();
    }

    /**
     * {@inheritdoc}
     */
    public function getForeignKey(): string
    {
        return $this->pivotParent
            ? parent::getForeignKey()
            : Model::getForeignKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getTable(): string
    {
        return $this->pivotParent
            ? parent::getTable()
            : Model::getTable();
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAtColumn(): string
    {
        return $this->pivotParent
            ? parent::getUpdatedAtColumn()
            : Model::getUpdatedAtColumn();
    }
}
