<?php

namespace Riesjart\Relaquent\Relations\Concerns;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Concerns\SupportsDefaultModels;

trait OneThroughTrait
{
    use SupportsDefaultModels;


    /**
     * @param Model $parent
     *
     * @return Model
     */
    protected function newRelatedInstanceFor(Model $parent): Model
    {
        return $this->related->newInstance();
    }

    // =======================================================================//
    //          Overrides
    // =======================================================================//

    /**
     * @return Model|mixed|null
     */
    public function getResults()
    {
        return $this->first() ?: $this->getDefaultFor($this->parent);
    }

    /**
     * @param array $models
     * @param string $relation
     *
     * @return array
     */
    public function initRelation(array $models, $relation): array
    {
        foreach ($models as $model) {

            $model->setRelation($relation, $this->getDefaultFor($this->parent));
        }

        return $models;
    }

    /**
     * @param array $models
     * @param EloquentCollection $results
     * @param string $relation
     *
     * @return array
     */
    public function match(array $models, EloquentCollection $results, $relation): array
    {
        $dictionary = $this->buildDictionary($results);

        foreach ($models as $model) {

            if (isset($dictionary[$key = $model->getAttribute($this->parentKey)])) {

                $value = $dictionary[$key];

                $model->setRelation($relation, reset($value));
            }
        }

        return $models;
    }
}
