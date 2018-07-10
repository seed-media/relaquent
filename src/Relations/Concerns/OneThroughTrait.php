<?php

namespace Riesjart\Relaquent\Relations\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as IlluminateBelongsToMany;

/**
 * Synced with illuminate/database v5.5.17
 */
trait OneThroughTrait
{
    /**
     * Indicates if a default model instance should be used.
     *
     * Alternatively, may be a Closure or array.
     *
     * @var Closure|array|bool
     */
    protected $withDefault;

    /**
     * Get the default value for this relation.
     *
     * @return Model|null
     */
    protected function getDefault(): ? Model
    {
        if ( ! $this->withDefault) {

            return null;
        }

        $instance = $this->related->newInstance();

        if (is_callable($this->withDefault)) {

            return call_user_func($this->withDefault, $instance) ?: $instance;
        }

        if (is_array($this->withDefault)) {

            $instance->forceFill($this->withDefault);
        }

        return $instance;
    }

    /**
     * Return a new model instance in case the relationship does not exist.
     *
     * @param Closure|array|bool $callback
     *
     * @return $this
     */
    public function withDefault($callback = true): IlluminateBelongsToMany
    {
        $this->withDefault = $callback;

        return $this;
    }

    // =======================================================================//
    //          Overrides
    // =======================================================================//

    /**
     * Get the results of the relationship.
     *
     * @return Model|null|mixed
     */
    public function getResults()
    {
        return $this->first() ?: $this->getDefault();
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param array $models
     * @param string $relation
     *
     * @return array
     */
    public function initRelation(array $models, $relation): array
    {
        foreach ($models as $model) {

            $model->setRelation($relation, $this->getDefault());
        }

        return $models;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param array $models
     * @param EloquentCollection $results
     * @param string $relation
     *
     * @return array
     */
    public function match(array $models, EloquentCollection $results, $relation): array
    {
        $dictionary = $this->buildDictionary($results);

        // Once we have the dictionary we can simply spin through the parent models to
        // link them up with their children using the keyed dictionary to make the
        // matching very convenient and easy work. Then we'll just return them.
        foreach ($models as $model) {

            if (isset($dictionary[$key = $model->{$this->parentKey}])) {

                $value = $dictionary[$key];

                $model->setRelation($relation, reset($value));
            }
        }

        return $models;
    }
}
