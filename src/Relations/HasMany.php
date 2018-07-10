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
     * @return HasOne
     */
    public function toHasOne(): HasOne
    {
        return new HasOne(
            $this->getRelated()->newQuery(), $this->getParent(), $this->getQualifiedForeignKeyName(), $this->localKey
        );
    }
}
