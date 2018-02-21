<?php

namespace Riesjart\Relaquent\Model\Concerns;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Riesjart\Relaquent\Exceptions\NotJoinableRelationTypeException;
use Riesjart\Relaquent\Relations\Contracts\JoinsRelationsContract;

trait JoinsRelations
{
    /**
     * @param array $relationNames
     * @param callable|null $callback
     * @param string $type
     * @param bool $where
     *
     * @return Closure
     */
    protected function getClosureForNestedRelationJoins(array $relationNames, callable $callback = null,
                                                        string $type = 'inner', bool $where = false): Closure
    {
        return function (EloquentQueryBuilder $query) use ($relationNames, $callback, $type, $where): void {

            $relationName = implode('.', $relationNames);

            $this->scopeJoinRelation($query, $relationName, $callback, $type, $where);
        };
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param Relation $relation
     * @param string $alias
     * @param callable $callback
     *
     * @return void
     */
    protected function handleRelationJoinCallback(EloquentQueryBuilder $query, Relation $relation,
                                                  string $alias, callable $callback): void
    {
        $related = $relation->getRelated();
        $relatedTable = $related->getTable();

        $related->setTable($alias);
        $query->setModel($related);

        $joinCount = method_exists($relation, 'performJoin') ? 2 : 1;
        $joins = array_slice($query->getQuery()->joins, -1 * $joinCount, $joinCount);
        $paramArr = Arr::prepend($joins, $query);

        call_user_func_array($callback, $paramArr);

        $related->setTable($relatedTable);
        $query->setModel($this);
    }

    // =======================================================================//
    //          Scopes
    // =======================================================================//

    /**
     * @param EloquentQueryBuilder $query
     * @param Relation $relation
     * @param string $alias
     * @param callable|null $callback
     * @param string $type
     * @param bool $where
     *
     * @return void
     *
     * @throws NotJoinableRelationTypeException
     */
    public function scopeCreateJoinFromRelation(EloquentQueryBuilder $query, Relation $relation, string $alias, callable $callback = null,
                                              string $type = 'inner', bool $where = false): void
    {
        if ($relation instanceof JoinsRelationsContract) {

            $relation->addAsJoin($query, $alias, $type, $where);

        } else {

            throw new NotJoinableRelationTypeException();
        }

        if ($callback) {

            $this->handleRelationJoinCallback($query, $relation, $alias, $callback);
        }
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param string $relationName
     * @param callable|null $callback
     * @param string $type
     * @param bool $where
     *
     * @return void
     */
    public function scopeJoinRelation(EloquentQueryBuilder $query, $relationName, callable $callback = null,
                                      string $type = 'inner', bool $where = false): void
    {
        $relationNames = explode('.', $relationName);

        if (count($relationNames) > 1) {

            $relationName = array_shift($relationNames);

            $callback = $this->getClosureForNestedRelationJoins($relationNames, $callback, $type, $where);
        }

        $lowerCasedRelationName = strtolower($relationName);

        if (Str::contains($lowerCasedRelationName, ' as ')) {

            $alias = Str::after($lowerCasedRelationName, ' as ');
            $relationName = Str::before($lowerCasedRelationName, ' as ');

        } else {

            $alias = Str::snake($relationName);
        }

        $relation = $this->$relationName();

        $this->scopeCreateJoinFromRelation($query, $relation, $alias, $callback, $type, $where);
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param string $relation
     * @param callable|null $callback
     * @param string $type
     *
     * @return void
     */
    public function scopeJoinRelationWhere(EloquentQueryBuilder $query, string $relation,
                                           callable $callback = null, string $type = 'inner'): void
    {
        $this->scopeJoinRelation($query, $relation, $callback, $type, true);
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param string $relation
     * @param callable|null $callback
     *
     * @return void
     */
    public function scopeLeftJoinRelation(EloquentQueryBuilder $query, string $relation, callable $callback = null): void
    {
        $this->scopeJoinRelation($query, $relation, $callback, 'left');
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param string $relation
     * @param callable|null $callback
     *
     * @return void
     */
    public function scopeLeftJoinRelationWhere(EloquentQueryBuilder $query, string $relation, callable $callback = null): void
    {
        $this->scopeJoinRelationWhere($query, $relation, $callback, 'left');
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param string $relation
     * @param callable|null $callback
     *
     * @return void
     */
    public function scopeRightJoinRelation(EloquentQueryBuilder $query, string $relation, callable $callback = null): void
    {
        $this->scopeJoinRelation($query, $relation, $callback, 'right');
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param string $relation
     * @param callable|null $callback
     *
     * @return void
     */
    public function scopeRightJoinRelationWhere(EloquentQueryBuilder $query, string $relation, callable $callback = null): void
    {
        $this->scopeJoinRelationWhere($query, $relation, $callback, 'right');
    }
}
