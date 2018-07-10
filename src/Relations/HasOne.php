<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Relations\HasOne as IlluminateHasOne;
use Riesjart\Relaquent\Relations\Concerns\HasOneOrManyTrait;
use Riesjart\Relaquent\Relations\Contracts\JoinsRelationsContract;

class HasOne extends IlluminateHasOne implements JoinsRelationsContract
{
    use HasOneOrManyTrait;

    //
}
