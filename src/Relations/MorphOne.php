<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Relations\MorphOne as IlluminateMorphOne;
use Riesjart\Relaquent\Relations\Concerns\HasOneOrManyTrait;
use Riesjart\Relaquent\Relations\Contracts\JoinsRelationsContract;

class MorphOne extends IlluminateMorphOne implements JoinsRelationsContract
{
    use HasOneOrManyTrait;

    //
}
