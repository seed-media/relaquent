<?php

namespace Riesjart\Relaquent\Model\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
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

/**
 * Synced with illuminate/database v5.5.17
 */
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
     * Define a one-to-one relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Riesjart\Relaquent\Relations\HasOne
     */
    public function hasOne($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return new HasOne($instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey);
    }

    /**
     * Define a polymorphic one-to-one relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Riesjart\Relaquent\Relations\MorphOne
     */
    public function morphOne($related, $name, $type = null, $id = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        list($type, $id) = $this->getMorphs($name, $type, $id);

        $table = $instance->getTable();

        $localKey = $localKey ?: $this->getKeyName();

        return new MorphOne($instance->newQuery(), $this, $table.'.'.$type, $table.'.'.$id, $localKey);
    }

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $ownerKey
     * @param  string  $relation
     * @return \Riesjart\Relaquent\Relations\BelongsTo
     */
    public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null)
    {
        // If no relation name was given, we will use this debug backtrace to extract
        // the calling method's name and use that as the relationship name as most
        // of the time this will be what we desire to use for the relationships.
        if (is_null($relation)) {
            $relation = $this->guessBelongsToRelation();
        }

        $instance = $this->newRelatedInstance($related);

        // If no foreign key was supplied, we can use a backtrace to guess the proper
        // foreign key name by using the name of the relationship function, which
        // when combined with an "_id" should conventionally match the columns.
        if (is_null($foreignKey)) {
            $foreignKey = Str::snake($relation).'_'.$instance->getKeyName();
        }

        // Once we have the foreign key names, we'll just create a new Eloquent query
        // for the related models and returns the relationship instance which will
        // actually be responsible for retrieving and hydrating every relations.
        $ownerKey = $ownerKey ?: $instance->getKeyName();

        return new BelongsTo(
            $instance->newQuery(), $this, $foreignKey, $ownerKey, $relation
        );
    }

    /**
     * Define a polymorphic, inverse one-to-one or many relationship.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @return \Riesjart\Relaquent\Relations\MorphTo
     */
    protected function morphEagerTo($name, $type, $id)
    {
        return new MorphTo(
            $this->newQuery()->setEagerLoads([]), $this, $id, null, $type, $name
        );
    }

    /**
     * Define a polymorphic, inverse one-to-one or many relationship.
     *
     * @param  string  $target
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @return \Riesjart\Relaquent\Relations\MorphTo
     */
    protected function morphInstanceTo($target, $name, $type, $id)
    {
        $instance = $this->newRelatedInstance(
            static::getActualClassNameForMorph($target)
        );

        return new MorphTo(
            $instance->newQuery(), $this, $id, $instance->getKeyName(), $type, $name
        );
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return \Riesjart\Relaquent\Relations\HasMany
     */
    public function hasMany($related, $foreignKey = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        $foreignKey = $foreignKey ?: $this->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        return new HasMany(
            $instance->newQuery(), $this, $instance->getTable().'.'.$foreignKey, $localKey
        );
    }

    /**
     * Define a has-many-through relationship.
     *
     * @param  string  $related
     * @param  string  $through
     * @param  string|null  $firstKey
     * @param  string|null  $secondKey
     * @param  string|null  $localKey
     * @param  string|null  $secondLocalKey
     * @return \Riesjart\Relaquent\Relations\HasManyThrough
     */
    public function hasManyThrough($related, $through, $firstKey = null, $secondKey = null, $localKey = null, $secondLocalKey = null)
    {
        $through = new $through;

        $firstKey = $firstKey ?: $this->getForeignKey();

        $secondKey = $secondKey ?: $through->getForeignKey();

        $localKey = $localKey ?: $this->getKeyName();

        $secondLocalKey = $secondLocalKey ?: $through->getKeyName();

        $instance = $this->newRelatedInstance($related);

        return new HasManyThrough($instance->newQuery(), $this, $through, $firstKey, $secondKey, $localKey, $secondLocalKey);
    }

    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Riesjart\Relaquent\Relations\MorphMany
     */
    public function morphMany($related, $name, $type = null, $id = null, $localKey = null)
    {
        $instance = $this->newRelatedInstance($related);

        // Here we will gather up the morph type and ID for the relationship so that we
        // can properly query the intermediate table of a relation. Finally, we will
        // get the table and create the relationship instances for the developers.
        list($type, $id) = $this->getMorphs($name, $type, $id);

        $table = $instance->getTable();

        $localKey = $localKey ?: $this->getKeyName();

        return new MorphMany($instance->newQuery(), $this, $table.'.'.$type, $table.'.'.$id, $localKey);
    }

    /**
     * Define a many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  string  $relation
     * @return \Riesjart\Relaquent\Relations\BelongsToMany
     */
    public function belongsToMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null,
                                  $parentKey = null, $relatedKey = null, $relation = null)
    {
        $this->normalizePivotTable($table, $pivot);

        // If no relationship name was passed, we will pull backtraces to get the
        // name of the calling function. We will use that function name as the
        // title of this relation since that is a great convention to apply.
        if (is_null($relation)) {
            $relation = $this->guessBelongsToManyRelation();
        }

        // First, we'll need to determine the foreign key and "other key" for the
        // relationship. Once we have determined the keys we'll make the query
        // instances as well as the relationship instances we need for this.
        $instance = $this->newRelatedInstance($related);

        $foreignPivotKey = $foreignPivotKey ?: $this->getForeignKey();

        $relatedPivotKey = $relatedPivotKey ?: $instance->getForeignKey();

        // If no table name was provided, we can guess it by concatenating the two
        // models using underscores in alphabetical order. The two model names
        // are transformed to snake case from their default CamelCase also.
        if (is_null($table)) {
            $table = $this->joiningTable($related);
        }

        return (new BelongsToMany(
            $instance->newQuery(), $this, $table, $foreignPivotKey,
            $relatedPivotKey, $parentKey ?: $this->getKeyName(),
            $relatedKey ?: $instance->getKeyName(), $relation
        ))->using($pivot);
    }

    /**
     * Define a polymorphic many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  bool  $inverse
     * @return \Riesjart\Relaquent\Relations\MorphToMany
     */
    public function morphToMany($related, $name, $table = null, $foreignPivotKey = null,
                                $relatedPivotKey = null, $parentKey = null,
                                $relatedKey = null, $inverse = false)
    {
        $this->normalizePivotTable($table, $pivot);

        $caller = $this->guessBelongsToManyRelation();

        // First, we will need to determine the foreign key and "other key" for the
        // relationship. Once we have determined the keys we will make the query
        // instances, as well as the relationship instances we need for these.
        $instance = $this->newRelatedInstance($related);

        $foreignPivotKey = $foreignPivotKey ?: $name.'_id';

        $relatedPivotKey = $relatedPivotKey ?: $instance->getForeignKey();

        // Now we're ready to create a new query builder for this related model and
        // the relationship instances for this relation. This relations will set
        // appropriate query constraints then entirely manages the hydrations.
        $table = $table ?: Str::plural($name);

        return (new MorphToMany(
            $instance->newQuery(), $this, $name, $table,
            $foreignPivotKey, $relatedPivotKey, $parentKey ?: $this->getKeyName(),
            $relatedKey ?: $instance->getKeyName(), $caller, $inverse
        ))->using($pivot);
    }
}
