<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany as IlluminateMorphToMany;
use Riesjart\Relaquent\Relations\Concerns\BelongsToManyTrait;

class MorphToMany extends IlluminateMorphToMany
{
    use BelongsToManyTrait;


    /**
     * @var string
     */
    protected $name;


    /**
     * @param EloquentQueryBuilder $query
     * @param Model $parent
     * @param string $name
     * @param string $table
     * @param string $foreignPivotKey
     * @param string $relatedPivotKey
     * @param string $parentKey
     * @param string $relatedKey
     * @param string|null $relationName
     * @param bool $inverse
     */
    public function __construct(EloquentQueryBuilder $query, Model $parent, string $name, string $table, string $foreignPivotKey,
                                string $relatedPivotKey, string $parentKey, string $relatedKey, string $relationName = null, bool $inverse = false)
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

        return new MorphMany($pivot->newQuery(), $this->parent, $pivot->qualifyColumn($this->morphType),
            $pivot->qualifyColumn($this->foreignPivotKey), $this->parentKey);
    }

    /**
     * @param bool $fresh
     *
     * @return MorphOneThrough
     */
    public function toMorphOneThrough(bool $fresh = false): MorphOneThrough
    {
        $query = $fresh ? $this->related->newQuery() : clone $this->query;

        $relation = new MorphOneThrough($query, $this->parent, $this->name, $this->table, $this->foreignPivotKey,
            $this->relatedPivotKey, $this->parentKey, $this->relatedKey, $this->relationName, $this->inverse);

        return $relation->withPivot($this->pivotColumns)
            ->using($this->using);
    }
}
