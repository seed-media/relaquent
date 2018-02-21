<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;

class BelongsToMorph extends BelongsTo
{
    /**
     * @var string
     */
    protected $morphType;


    /**
     * @param EloquentQueryBuilder $query
     * @param Model $child
     * @param string $type
     * @param string $foreignKey
     * @param string $ownerKey
     * @param string $relation
     */
    public function __construct(EloquentQueryBuilder $query, Model $child, string $type, string $foreignKey,
                                string $ownerKey, string $relation)
    {
        $this->morphType = $type;

        parent::__construct($query, $child, $foreignKey, $ownerKey, $relation);
    }

    // =======================================================================//
    //          Overrides
    // =======================================================================//

    /**
     * @param array $models
     *
     * @return void
     */
    public function addEagerConstraints(array $models): void
    {
        $models = EloquentCollection::make($models)
            ->where($this->morphType, $this->related->getMorphClass())
            ->all();

        parent::addEagerConstraints($models);
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param EloquentQueryBuilder $parentQuery
     * @param array|mixed $columns
     *
     * @return EloquentQueryBuilder
     */
    public function getRelationExistenceQuery(EloquentQueryBuilder $query, EloquentQueryBuilder $parentQuery,
                                              $columns = ['*']): EloquentQueryBuilder
    {
        return parent::getRelationExistenceQuery($query, $parentQuery, $columns)
            ->where($this->parent->qualifyColumn($this->morphType), $this->related->getMorphClass());
    }

    /**
     * @return Model|mixed|null
     */
    public function getResults()
    {
        if ($this->parent->getAttribute($this->morphType) === $this->related->getMorphClass()) {

            return parent::getResults();
        }

        return $this->getDefaultFor($this->parent);
    }
}
