<?php

namespace Riesjart\Relaquent\Model\Concerns;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Riesjart\Relaquent\Relations\BelongsTo;
use Riesjart\Relaquent\Relations\BelongsToMany;
use Riesjart\Relaquent\Relations\BelongsToMorph;
use Riesjart\Relaquent\Relations\HasMany;
use Riesjart\Relaquent\Relations\HasManyThrough;
use Riesjart\Relaquent\Relations\HasOne;
use Riesjart\Relaquent\Relations\HasOneThrough;
use Riesjart\Relaquent\Relations\MorphMany;
use Riesjart\Relaquent\Relations\MorphOne;
use Riesjart\Relaquent\Relations\MorphOneThrough;
use Riesjart\Relaquent\Relations\MorphTo;
use Riesjart\Relaquent\Relations\MorphToMany;
use Riesjart\Relaquent\Relations\Pivot;

trait HasRelaquentRelationships
{
    /**
     * @param string $related
     * @param string|null $name
     * @param string|null $type
     * @param string|null $id
     * @param string|null $ownerKey
     *
     * @return BelongsToMorph
     */
    public function belongsToMorph(string $related, string $name = null, string $type = null,
                                   string $id = null, string $ownerKey = null): BelongsToMorph
    {
        return $this->morphTo($name, $type, $id)
            ->toBelongsToMorph($related, $ownerKey);
    }

    /**
     * @param string $related
     * @param string|null $table
     * @param string|null $foreignKey
     * @param string|null $relatedKey
     * @param string|null $relation
     *
     * @return HasOneThrough
     */
    public function hasOneThrough(string $related, string $table = null, string $foreignKey = null,
                                  string $relatedKey = null, string $relation = null): HasOneThrough
    {
        return $this->belongsToMany($related, $table, $foreignKey, $relatedKey, $relation)
            ->toHasOneThrough();
    }

    /**
     * @param string $related
     * @param string $name
     * @param string|null $table
     * @param string|null $foreignKey
     * @param string|null $relatedKey
     * @param bool $inverse
     *
     * @return MorphOneThrough
     */
    public function morphOneThrough(string $related, string $name, string $table = null, string $foreignKey = null,
                                    string $relatedKey = null, bool $inverse = false): MorphOneThrough
    {
        return $this->morphToMany($related, $name, $table, $foreignKey, $relatedKey, $inverse)
            ->toMorphOneThrough();
    }

    /**
     * @param string|null $table
     * @param string|null $pivotClass
     *
     * @return void
     */
    protected function normalizePivotTable(&$table, string &$pivotClass = null): void
    {
        if (class_exists($table)) {

            $model = new $table;

            if ($model instanceof Model) {

                if ($model instanceof Pivot) {

                    $pivotClass = $table;
                }

                $table = $model->getTable();
            }
        }
    }

    // =======================================================================//
    //          Overrides
    // =======================================================================//

    /**
     * @param string $related
     * @param string|null $table
     * @param string|null $foreignPivotKey
     * @param string|null $relatedPivotKey
     * @param string|null $parentKey
     * @param string|null $relatedKey
     * @param string|null $relation
     *
     * @return BelongsToMany
     */
    public function belongsToMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null,
                                  $parentKey = null, $relatedKey = null, $relation = null): BelongsToMany
    {
        $this->normalizePivotTable($table, $pivot);

        return parent::belongsToMany($related, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relation)
            ->using($pivot);
    }

    /**
     * @param string $related
     * @param string $name
     * @param string|null $table
     * @param string|null $foreignPivotKey
     * @param string|null $relatedPivotKey
     * @param string|null $parentKey
     * @param string|null $relatedKey
     * @param bool $inverse
     *
     * @return MorphToMany
     */
    public function morphToMany($related, $name, $table = null, $foreignPivotKey = null, $relatedPivotKey = null,
                                $parentKey = null, $relatedKey = null, $inverse = false): MorphToMany
    {
        $this->normalizePivotTable($table, $pivot);

        return parent::morphToMany($related, $name, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $inverse)
            ->using($pivot);
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param Model $child
     * @param string $foreignKey
     * @param string $ownerKey
     * @param string $relation
     *
     * @return BelongsTo
     */
    protected function newBelongsTo(EloquentQueryBuilder $query, Model $child, $foreignKey, $ownerKey, $relation): BelongsTo
    {
        return new BelongsTo($query, $child, $foreignKey, $ownerKey, $relation);
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param Model $parent
     * @param string $table
     * @param string $foreignPivotKey
     * @param string $relatedPivotKey
     * @param string $parentKey
     * @param string $relatedKey
     * @param string|null $relationName
     *
     * @return BelongsToMany
     */
    protected function newBelongsToMany(EloquentQueryBuilder $query, Model $parent, $table, $foreignPivotKey, $relatedPivotKey,
                                        $parentKey, $relatedKey, $relationName = null): BelongsToMany
    {
        return new BelongsToMany($query, $parent, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName);
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param Model $parent
     * @param string $foreignKey
     * @param string $localKey
     *
     * @return HasMany
     */
    protected function newHasMany(EloquentQueryBuilder $query, Model $parent, $foreignKey, $localKey): HasMany
    {
        return new HasMany($query, $parent, $foreignKey, $localKey);
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param Model $farParent
     * @param Model $throughParent
     * @param string $firstKey
     * @param string $secondKey
     * @param string $localKey
     * @param string $secondLocalKey
     *
     * @return HasManyThrough
     */
    protected function newHasManyThrough(EloquentQueryBuilder $query, Model $farParent, Model $throughParent,
                                         $firstKey, $secondKey, $localKey, $secondLocalKey): HasManyThrough
    {
        return new HasManyThrough($query, $farParent, $throughParent, $firstKey, $secondKey, $localKey, $secondLocalKey);
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param Model $parent
     * @param $foreignKey
     * @param $localKey
     *
     * @return HasOne
     */
    protected function newHasOne(EloquentQueryBuilder $query, Model $parent, $foreignKey, $localKey): HasOne
    {
        return new HasOne($query, $parent, $foreignKey, $localKey);
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param Model $parent
     * @param string $type
     * @param string $id
     * @param string $localKey
     *
     * @return MorphMany
     */
    protected function newMorphMany(EloquentQueryBuilder $query, Model $parent, $type, $id, $localKey): MorphMany
    {
        return new MorphMany($query, $parent, $type, $id, $localKey);
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param Model $parent
     * @param string $type
     * @param string $id
     * @param string $localKey
     *
     * @return MorphOne
     */
    protected function newMorphOne(EloquentQueryBuilder $query, Model $parent, $type, $id, $localKey): MorphOne
    {
        return new MorphOne($query, $parent, $type, $id, $localKey);
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param Model $parent
     * @param string $foreignKey
     * @param string $ownerKey
     * @param string $type
     * @param string $relation
     *
     * @return MorphTo
     */
    protected function newMorphTo(EloquentQueryBuilder $query, Model $parent, $foreignKey, $ownerKey, $type, $relation): MorphTo
    {
        return new MorphTo($query, $parent, $foreignKey, $ownerKey, $type, $relation);
    }

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
     *
     * @return MorphToMany
     */
    protected function newMorphToMany(EloquentQueryBuilder $query, Model $parent, $name, $table, $foreignPivotKey, $relatedPivotKey,
                                      $parentKey, $relatedKey, $relationName = null, $inverse = false): MorphToMany
    {
        return new MorphToMany($query, $parent, $name, $table, $foreignPivotKey, $relatedPivotKey,
            $parentKey, $relatedKey, $relationName, $inverse);
    }
}
