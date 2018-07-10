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
     * @return MorphOne
     */
    public function toMorphOne(): MorphOne
    {
        return new MorphOne($this->getRelated()->newQuery(), $this->getParent(), $this->getMorphType(),
            $this->getQualifiedForeignKeyName(), $this->localKey);
    }
}
