<?php

namespace Tranquangkhuong\DataTransformer\Abstract;

use Tranquangkhuong\DataTransformer\Enum\DefaultVarConfig;
use Tranquangkhuong\DataTransformer\Enum\FillType;

/**
 * Base transformer
 */
abstract class Transformer
{
    /**
     * configuration to handle data conversion
     * 
     * @var array
     */
    protected $config = [];

    /**
     * Case get value
     * 
     * @var string
     */
    protected string $case;

    /**
     * Data to be converted
     * 
     * @var array
     */
    protected array $data;

    /**
     * Result after conversion
     * 
     * @var array
     */
    protected array $result = [];

    /**
     * Name of the original application
     * 
     * @var string
     */
    protected string $fromApp;

    /**
     * Name of the application to be converted to
     * 
     * @var string
     */
    protected string $toApp;

    /**
     * Type fill value
     * 
     * @var FillType
     */
    protected FillType $fillType = FillType::ALL;

    /**
     * Array key separator
     * 
     * @var string
     */
    protected string $arrayKeySeparator = DefaultVarConfig::ARRAY_KEY_SEPARATOR->value;

    /**
     * Group value separator
     * 
     * @var string
     */
    protected string $groupValueSeparator = DefaultVarConfig::GROUP_VALUE_SEPARATOR->value;

    /**
     * Set config
     * 
     * @param array|string $config
     * @return static
     */
    abstract public function config(array|string $config): static;

    /**
     * Set data
     * 
     * @param array $data
     * @return static
     */
    public function data(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set case
     * 
     * @param string $case
     * @return static
     */
    public function case(string $case): static
    {
        $this->case = $case;
        return $this;
    }

    /**
     * Set fill type
     * 
     * @param FillType $fillType
     * @return static
     */
    public function fillType(FillType $fillType): static
    {
        $this->fillType = $fillType;
        return $this;
    }

    /**
     * Set array key separator
     * 
     * @param string $separator
     * @return static
     */
    public function arrayKeySeparator(string $separator): static
    {
        $this->arrayKeySeparator = $separator;
        return $this;
    }

    /**
     * Set group value separator
     * 
     * @param string $separator
     * @return static
     */
    public function groupValueSeparator(string $separator): static
    {
        $this->groupValueSeparator = $separator;
        return $this;
    }

    /**
     * Transform data
     * 
     * @param string $from
     * @param string $to
     */
    abstract public function transform(string $from, string $to): array;

    /**
     * Check if field is allowed
     * 
     * @param array $config
     * @return bool
     */
    protected function isFieldAllowed(array $config): bool
    {
        if ($config['key'] == '') return false;

        $cases = (array) $config['case'];
        return in_array($this->case, $cases);
    }

    /**
     * Transform value
     * 
     * @param mixed $value
     * @param array $config
     * @return mixed
     */
    protected function transformValue($value, array $config): mixed
    {
        if (isset($config['group_value'])) {
            $position = $config['group_value_position'] ?? 0;
            if ($config['group_value'] == 'split') {
                $arrGrpVal = explode($this->groupValueSeparator, $value);
                $value = $arrGrpVal[$position];
            } else if ($config['group_value'] == 'merge') {
                $temp = &$this->result;
                $keys = explode($this->arrayKeySeparator, $config['key']);
                foreach ($keys as $k) {
                    if (!isset($temp[$k])) {
                        $temp[$k] = [];
                    }
                    $temp = &$temp[$k];
                }
                if (!is_array($temp)) {
                    $arr = explode($this->groupValueSeparator, $temp);
                    if (((int) $config['group_value_position'] - count($arr)) === 0) {
                        $arr[] = $value;
                    }
                    $value = implode($this->groupValueSeparator, $arr);
                }
                unset($temp);
            }
        }

        return match ($config['type'] ?? '') {
            'string' => $this->transformString($value, $config),
            'date'   => $this->transformDate($value, $config),
            default  => $value,
        };
    }

    /**
     * Transform string
     * 
     * @param mixed $value
     * @param array $config
     * @return string
     */
    protected function transformString(mixed $value, array $config): string
    {
        if (!is_string($value)) {
            return '';
        }

        return match ($config['format'] ?? '') {
            'lower' => mb_strtolower($value),
            'upper' => mb_strtoupper($value),
            'capital' => mb_ucfirst($value),
            default => $value,
        };
    }

    /**
     * Transform date
     * 
     * @param mixed $value
     * @param array $config
     * @return string
     */
    protected function transformDate(mixed $value, array $config): string
    {
        if (
            is_null($value) || is_array($value) ||
            ((string) $value == '')
        ) {
            return '';
        }

        $formats = [
            'Y-m-d',
            'd/m/Y',
            'd-m-Y',
            'Y/m/d',
            'Y/m/d H:i:s',
            'Y-m-d H:i:s',
            'd/m/Y H:i:s',
            'd-m-Y H:i:s',
            'Y-m-d H:i:s.v',
            'Y.m.d',
            'd.m.Y',
            'Ymd',
            'YmdHis',
            'dmY',
            'dmYHis',
            'Y-m',
            'Y/m',
            'Y.m',
            'm-Y',
            'm/Y',
            'm.Y',
            'Y',
        ];
        foreach ($formats as $format) {
            $dateTime = \DateTime::createFromFormat($format, $value);
            if ($dateTime) {
                return $dateTime->format($config['format'] ?? 'd/m/Y');
            }
        }

        return $value;
    }
}
