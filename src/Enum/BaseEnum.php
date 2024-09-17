<?php

namespace Tranquangkhuong\DataTransformer\Enum;

use ReflectionEnum;

/**
 * Additional methods supporting Enum
 */
trait BaseEnum
{
    /**
     * Get instance from `name`
     * 
     * @param mixed $name
     */
    public static function tryCase($name): ?static
    {
        $r = new ReflectionEnum(static::class);
        return $r->hasCase($name) ? $r->getConstant($name) : null;
    }

    /**
     * Get all `name`
     * 
     * @return array
     */
    public static function names(): array
    {
        return array_column(static::cases(), 'name');
    }

    /**
     * Get all `value`
     * 
     * @return array
     */
    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }

    /**
     * Get all `name` follow `value` base on `Enum::cases()`
     * * `value` is `key` on array
     * * `name` is `value` on array
     * 
     * @return array<value,name>
     */
    public static function allNameByValue(): array
    {
        return array_column(static::cases(), 'name', 'value');
    }

    /**
     * Get all `value` follow `name` base on `Enum::cases()`
     * * `name` is `key` on array
     * * `value` is `value` on array
     * 
     * @return array<name,value>
     */
    public static function allValueByName(): array
    {
        return array_column(static::cases(), 'value', 'name');
    }

    /**
     * Check if current instance equals another instance/value
     * 
     * @param mixed $value value or instance of Enum
     * @return bool
     */
    public function is($value): bool
    {
        if ($value instanceof static) {
            return $this->value === $value->value;
        }

        return $this->value === $value;
    }

    /**
     * Check if current instance NOT equals another instance/value
     * 
     * @param mixed $value value or instance of Enum
     * @return bool
     */
    public function isNot($value): bool
    {
        return !$this->is($value);
    }

    /**
     * Check if current enum value is in input array
     * * If there is only 1 element equal to the current enum, return true; 
     * otherwise return false.
     *
     * @param iterable<mixed> $values An array of instances or values
     * @return bool
     */
    public function in(iterable $values): bool
    {
        if (is_iterable($values)) {
            foreach ($values as $value) {
                if ($this->is($value)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if current enum value is NOT in input array
     * * If the array does not contain the current enum, return true; otherwise return false.
     *
     * @param iterable<mixed> $values An array of instances or values
     * @return bool
     */
    public function notIn(iterable $values): bool
    {
        if (is_iterable($values)) {
            foreach ($values as $value) {
                if ($this->is($value)) {
                    return false;
                }
            }
        }

        return true;
    }
}
