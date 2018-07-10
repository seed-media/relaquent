<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;

/**
 * Synced with illuminate/database v5.5.17
 */
class MorphPivot extends Pivot
{
    /**
     * The type of the polymorphic relation.
     *
     * Explicitly define this so it's not included in saved attributes.
     *
     * @var string
     */
    protected $morphType;

    /**
     * The value of the polymorphic relation.
     *
     * Explicitly define this so it's not included in saved attributes.
     *
     * @var string
     */
    protected $morphClass;

    /**
     * Set the keys for a save update query.
     *
     * @param EloquentQueryBuilder $query
     *
     * @return EloquentQueryBuilder
     */
    protected function setKeysForSaveQuery(EloquentQueryBuilder $query): EloquentQueryBuilder
    {
        if ( ! isset($this->attributes[$this->getKeyName()])) {

            $query->where($this->morphType, $this->morphClass);
        }

        return parent::setKeysForSaveQuery($query);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDeleteQuery(): EloquentQueryBuilder
    {
        return parent::getDeleteQuery()
            ->where($this->morphType, $this->morphClass);
    }

    /**
     * Set the morph type for the pivot.
     *
     * @param string $morphType
     *
     * @return $this
     */
    public function setMorphType(string $morphType): MorphPivot
    {
        $this->morphType = $morphType;

        return $this;
    }

    /**
     * Set the morph class for the pivot.
     *
     * @param string $morphClass
     *
     * @return $this
     */
    public function setMorphClass(string $morphClass): MorphPivot
    {
        $this->morphClass = $morphClass;

        return $this;
    }
}
