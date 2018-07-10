<?php

namespace Riesjart\Relaquent\Model\Concerns;

use Closure;
use Exception;
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
     * @param Closure|null $closure
     * @param string $type
     * @param bool $where
     *
     * @return Closure
     */
    protected function getClosureForNestedRelationJoins(array $relationNames, Closure $closure = null, 
                                                        string $type = 'inner', bool $where = false): Closure
    {
        return function (EloquentQueryBuilder $query) use ($relationNames, $closure, $type, $where) {

            $relationName = implode('.', $relationNames);

            $query->joinRelation($relationName, $closure, $type, $where);
        };
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param Relation $relation
     * @param string $alias
     * @param Closure $closure
     *
     * @return void
     */
    protected function handleRelationJoinClosure(EloquentQueryBuilder $query, Relation $relation,
                                                 string $alias, Closure $closure): void
    {
        $related = $relation->getRelated();
        $relatedTable = $related->getTable();

        $related->setTable($alias);
        $query->setModel($related);

        $joinCount = method_exists($relation, 'performJoin') ? 2 : 1;
        $joins = array_slice($query->getQuery()->joins, -1 * $joinCount, $joinCount);
        $paramArr = Arr::prepend($joins, $query);

        call_user_func_array($closure, $paramArr);

        $related->setTable($relatedTable);
        $query->setModel($this);
    }

    // =======================================================================//
    //          Scopes
    // =======================================================================//

    /**
     * @param EloquentQueryBuilder $query
     * @param string $relationName
     * @param Closure|null $closure
     * @param string $type
     * @param bool $where
     *
     * @return void
     *
     * @throws Exception
     */
    public function scopeJoinRelation(EloquentQueryBuilder $query, $relationName, Closure $closure = null, 
                                      string $type = 'inner', bool $where = false): void
    {
        $relationNames = explode('.', $relationName);

        if (count($relationNames) > 1) {

            $relationName = array_shift($relationNames);

            $closure = $this->getClosureForNestedRelationJoins($relationNames, $closure, $type, $where);
        }

        $aliasPosition = strpos(strtolower($relationName), ' as ');

        if ($aliasPosition !== false) {

            $alias = trim(substr($relationName, $aliasPosition + 4));
            $relationName = trim(substr($relationName, 0, $aliasPosition));

        } else {

            $alias = Str::snake($relationName);
        }

        $relation = $this->$relationName();

        $this->scopeCreateJoinOfRelation($query, $relation, $alias, $closure, $type, $where);
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param string $relation
     * @param Closure|null $closure
     * @param string $type
     *
     * @return void
     */
    public function scopeJoinRelationWhere(EloquentQueryBuilder $query, string $relation,
                                           Closure $closure = null, string $type = 'inner'): void
    {
        $this->scopeJoinRelation($query, $relation, $closure, $type, true);
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param string $relation
     * @param Closure|null $closure
     *
     * @return void
     */
    public function scopeLeftJoinRelation(EloquentQueryBuilder $query, string $relation, Closure $closure = null): void
    {
        $this->scopeJoinRelation($query, $relation, $closure, 'left');
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param string $relation
     * @param Closure|null $closure
     *
     * @return void
     */
    public function scopeLeftJoinRelationWhere(EloquentQueryBuilder $query, string $relation, Closure $closure = null): void
    {
        $this->scopeJoinRelationWhere($query, $relation, $closure, 'left');
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param string $relation
     * @param Closure|null $closure
     *
     * @return void
     */
    public function scopeRightJoinRelation(EloquentQueryBuilder $query, string $relation, Closure $closure = null): void
    {
        $this->scopeJoinRelation($query, $relation, $closure, 'right');
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param string $relation
     * @param Closure|null $closure
     *
     * @return void
     */
    public function scopeRightJoinRelationWhere(EloquentQueryBuilder $query, string $relation, Closure $closure = null): void
    {
        $this->scopeJoinRelationWhere($query, $relation, $closure, 'right');
    }

    /**
     * @param EloquentQueryBuilder $query
     * @param Relation $relation
     * @param string $alias
     * @param Closure|null $closure
     * @param string $type
     * @param bool $where
     *
     * @return void
     *
     * @throws Exception
     */
    public function scopeCreateJoinOfRelation(EloquentQueryBuilder $query, Relation $relation, string $alias, Closure $closure = null,
                                              string $type = 'inner', bool $where = false): void
    {
        if ($relation instanceof JoinsRelationsContract) {

            $relation->addAsJoin($query, $alias, $type, $where);

        } else {

            throw new NotJoinableRelationTypeException();
        }

        if ($closure) {

            $this->handleRelationJoinClosure($query, $relation, $alias, $closure);
        }
    }
}
