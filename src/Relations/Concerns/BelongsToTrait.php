<?php

namespace Riesjart\Relaquent\Relations\Concerns;

use Illuminate\Database\Eloquent\Model;

trait BelongsToTrait
{
    /**
     * @return mixed
     */
    public function getForeignValue()
    {
        return $this->parent->getAttribute($this->getForeignKey());
    }

    /**
     * @param Model|int|string $other
     *
     * @return bool
     */
    public function isNot($other): bool
    {
        return ! $this->is($other);
    }

    /**
     * @return bool
     */
    public function notNull()
    {
        return ! $this->isNull();
    }
}
