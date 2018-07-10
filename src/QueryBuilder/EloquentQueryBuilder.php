<?php

namespace Riesjart\Relaquent\QueryBuilder;

use Illuminate\Database\Eloquent\Builder as IlluminateEloquentQueryBuilder;
use Riesjart\Relaquent\QueryBuilder\Concerns\EagerLoadsPivotRelation;

class EloquentQueryBuilder extends IlluminateEloquentQueryBuilder
{
    use EagerLoadsPivotRelation;

    //
}
