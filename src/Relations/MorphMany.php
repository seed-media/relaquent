<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Relations\MorphMany as IlluminateMorphMany;
use Riesjart\Relaquent\Relations\Concerns\HasOneOrManyTrait;
use Riesjart\Relaquent\Relations\Contracts\JoinsRelationsContract;

class MorphMany extends IlluminateMorphMany implements JoinsRelationsContract
{
    use HasOneOrManyTrait;


    // =======================================================================//
    //          Converters
    // =======================================================================//

    /**
     * @param bool $fresh
     *
     * @return MorphOne
     */
    public function toMorphOne(bool $fresh = false): MorphOne
    {
        $query = $fresh ? $this->related->newQuery() : clone $this->query;

        return new MorphOne($query, $this->parent, $this->getMorphType(),
            $this->getQualifiedForeignKeyName(), $this->localKey);
    }
}
