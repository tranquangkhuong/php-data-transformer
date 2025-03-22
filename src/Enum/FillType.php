<?php

namespace Tranquangkhuong\DataTransformer\Enum;

/**
 * Type fill value
 */
enum FillType: int
{
    use BaseEnum;

    case ALL = 0;
    case SKIP_NULL = 1;
    case SKIP_EMPTY = 2;
    case SKIP_NULL_EMPTY = 3;
}
