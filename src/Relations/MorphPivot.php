<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Support\Str;

class MorphPivot extends Pivot
{
    /**
     * @var string
     */
    protected $morphClass;

    /**
     * @var string
     */
    protected $morphType;


    /**
     * @param string $morphClass
     *
     * @return MorphPivot
     */
    public function setMorphClass(string $morphClass): MorphPivot
    {
        $this->morphClass = $morphClass;

        return $this;
    }

    /**
     * @param string $morphType
     *
     * @return $this
     */
    public function setMorphType(string $morphType): MorphPivot
    {
        $this->morphType = $morphType;

        return $this;
    }

    // =======================================================================//
    //          Overrides
    // =======================================================================//

    /**
     * @return EloquentQueryBuilder
     */
    protected function getDeleteQuery(): EloquentQueryBuilder
    {
        return parent::getDeleteQuery()
            ->where($this->morphType, $this->morphClass);
    }

    /**
     * @return mixed
     */
    public function getQueueableId()
    {
        if (isset($this->attributes[$this->getKeyName()])) {

            return $this->getKey();
        }

        return sprintf(
            '%s:%s:%s:%s:%s:%s',
            $this->foreignKey, $this->getAttribute($this->foreignKey),
            $this->relatedKey, $this->getAttribute($this->relatedKey),
            $this->morphType, $this->morphClass
        );
    }


    /**
     * @param array $ids
     *
     * @return EloquentQueryBuilder
     */
    protected function newQueryForCollectionRestoration(array $ids): EloquentQueryBuilder
    {
        if ( ! Str::contains($ids[0], ':')) {

            return parent::newQueryForRestoration($ids);
        }

        $query = $this->newQueryWithoutScopes();

        foreach ($ids as $id) {

            $segments = explode(':', $id);

            $query->orWhere(function (EloquentQueryBuilder $query) use ($segments): EloquentQueryBuilder {

                return $query->where($segments[0], $segments[1])
                    ->where($segments[2], $segments[3])
                    ->where($segments[4], $segments[5]);
            });
        }

        return $query;
    }

    /**
     * @param array|int $ids
     *
     * @return EloquentQueryBuilder
     */
    public function newQueryForRestoration($ids): EloquentQueryBuilder
    {
        if (is_array($ids)) {

            return $this->newQueryForCollectionRestoration($ids);
        }

        if ( ! Str::contains($ids, ':')) {

            return parent::newQueryForRestoration($ids);
        }

        $segments = explode(':', $ids);

        return $this->newQueryWithoutScopes()
            ->where($segments[0], $segments[1])
            ->where($segments[2], $segments[3])
            ->where($segments[4], $segments[5]);
    }

    /**
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
}
