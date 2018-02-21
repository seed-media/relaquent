<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Relations\HasMany as IlluminateHasMany;
use Riesjart\Relaquent\Relations\Concerns\HasOneOrManyTrait;
use Riesjart\Relaquent\Relations\Contracts\JoinsRelationsContract;

class HasMany extends IlluminateHasMany implements JoinsRelationsContract
{
    use HasOneOrManyTrait;


    // =======================================================================//
    //          Converters
    // =======================================================================//

    /**
     * @param bool $fresh
     *
     * @return HasOne
     */
    public function toHasOne(bool $fresh = false): HasOne
    {
        $query = $fresh ? $this->related->newQuery() : clone $this->query;

        return new HasOne(
            $query, $this->parent, $this->getQualifiedForeignKeyName(), $this->localKey
        );
    }
}
