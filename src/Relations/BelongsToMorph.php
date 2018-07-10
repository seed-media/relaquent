<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;

/**
 * Synced with illuminate/database v5.5.17
 */
class BelongsToMorph extends BelongsTo
{
    /**
     * The type of the polymorphic relation.
     *
     * @var string
     */
    protected $morphType;


    // =======================================================================//
    //          Overrides
    // =======================================================================//

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

    /**
     * {@inheritdoc}
     */
    public function getResults()
    {
        if ($this->getParent()->getAttribute($this->morphType) === $this->getRelated()->getMorphClass()) {

            return parent::getResults();
        }

        return $this->getDefaultFor($this->parent);
    }

    /**
     * {@inheritdoc}
     */
    public function addEagerConstraints(array $models)
    {
        $models = EloquentCollection::make($models)
            ->where($this->morphType, $this->getRelated()->getMorphClass())
            ->all();

        parent::addEagerConstraints($models);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationExistenceQuery(EloquentQueryBuilder $query, EloquentQueryBuilder $parentQuery, $columns = ['*'])
    {
        return parent::getRelationExistenceQuery($query, $parentQuery, $columns)
            ->where($this->getParent()->getTable() . '.' . $this->morphType, '=', $this->getRelated()->getMorphClass());
    }
}
