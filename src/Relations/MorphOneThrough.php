<?php

namespace Riesjart\Relaquent\Relations;

use Riesjart\Relaquent\Relations\Concerns\OneThroughTrait;

class MorphOneThrough extends MorphToMany
{
    use OneThroughTrait;
}
