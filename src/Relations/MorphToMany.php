<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany as IlluminateMorphToMany;
use Riesjart\Relaquent\Relations\Concerns\BelongsToManyTrait;

/**
 * Synced with illuminate/database v5.5.17
 */
class MorphToMany extends IlluminateMorphToMany
{
    use BelongsToManyTrait;

    /**
     * @var string
     */
    protected $name;


    // =======================================================================//
    //          Overrides
    // =======================================================================//

    /**
     * {@inheritdoc}
     */
    public function __construct(EloquentQueryBuilder $query, Model $parent, $name, $table, $foreignPivotKey,
                                $relatedPivotKey, $parentKey, $relatedKey, $relationName = null, $inverse = false)
    {
        $this->name = $name;

        parent::__construct($query, $parent, $name, $table, $foreignPivotKey, $relatedPivotKey, $parentKey,
            $relatedKey, $relationName, $inverse);
    }

    // =======================================================================//
    //          Converters
    // =======================================================================//

    /**
     * @param string|null $pivotClass
     *
     * @return MorphMany
     */
    public function toMorphMany(string $pivotClass = null): MorphMany
    {
        $pivotClass = $pivotClass ?: $this->guessPivotClass();
        $pivot = new $pivotClass;

        $parent = $this->getParent();

        return new MorphMany($pivot->newQuery(), $parent, $pivot->getTable() . '.' . $this->getMorphType(),
            $pivot->getTable() . '.' . $this->foreignPivotKey, $this->parentKey);
    }

    /**
     * @return MorphOneThrough
     */
    public function toMorphOneThrough(): MorphOneThrough
    {
        $relation = new MorphOneThrough($this->getRelated()->newQuery(), $this->getParent(), $this->name, $this->getTable(),
            $this->foreignPivotKey, $this->relatedPivotKey, $this->parentKey, $this->relatedKey, $this->getRelationName(), $this->inverse);

        return $relation
            ->withPivot($this->pivotColumns)
            ->using($this->using);
    }
}
