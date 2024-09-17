<?php

namespace Tranquangkhuong\DataTransformer\Enum;

/**
 * Default variable configuration
 */
enum DefaultVarConfig: string
{
    use BaseEnum;

    case ARRAY_KEY_SEPARATOR = '.';
    case GROUP_VALUE_SEPARATOR = ',';
}
