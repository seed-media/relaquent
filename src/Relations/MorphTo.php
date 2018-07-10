<?php

namespace Riesjart\Relaquent\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo as IlluminateMorphTo;
use Riesjart\Relaquent\Relations\Concerns\BelongsToTrait;

class MorphTo extends IlluminateMorphTo
{
    use BelongsToTrait;

    /**
     * @return string|null
     */
    public function getMorphTypeValue(): ? string
    {
        return $this->parent->getAttribute($this->getMorphType());
    }

    /**
     * @param Model|int|string $otherKey
     * @param string $otherType
     *
     * @return bool
     */
    public function is($otherKey, string $otherType = null): bool
    {
        if ($otherKey instanceof Model) {

            return $this->getForeignValue() === $otherKey->getAttribute($this->ownerKey) &&
                $this->getMorphTypeValue() === $otherKey->getMorphClass() &&
                $this->related->getTable() === $otherKey->getTable() &&
                $this->related->getConnectionName() === $otherKey->getConnectionName();
        }

        return $this->getForeignValue() == $otherKey && $this->getMorphTypeValue() === $otherType;
    }

    /**
     * @return bool
     */
    public function isDirty(): bool
    {
        return $this->parent->isDirty([$this->getForeignKey(), $this->getMorphType()]);
    }

    /**
     * @return bool
     */
    public function isNull(): bool
    {
        return is_null($this->getForeignValue()) || is_null($this->getMorphTypeValue());
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isOfType($type): bool
    {
        $value = $this->getMorphTypeValue();

        if (class_exists($type)) {

            return $value === (new $type)->getMorphClass();
        }

        return $value === $type;
    }

    // =======================================================================//
    //          Converters
    // =======================================================================//

    /**
     * @param string $related
     * @param string|null $ownerKey
     *
     * @return BelongsToMorph
     */
    public function toBelongsToMorph(string $related, string $ownerKey = null): BelongsToMorph
    {
        $instance = new $related;

        $ownerKey = $ownerKey ?: $instance->getKeyName();

        return new BelongsToMorph($instance->newQuery(), $this->getParent(), $this->getMorphType(), $this->getForeignKey(),
            $ownerKey, $this->getRelation());
    }
}
