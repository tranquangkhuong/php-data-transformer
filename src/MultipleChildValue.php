<?php

namespace Tranquangkhuong\DataTransformer;

use Tranquangkhuong\DataTransformer\Abstract\Transformer;
use Tranquangkhuong\DataTransformer\Enum\FillType;

/**
 * Transform multiple children data
 */
class MultipleChildValue extends Transformer
{
    /**
     * Prefix key from
     * 
     * @var string
     */
    private string $prefixKeyFrom;

    /**
     * Prefix key to
     * 
     * @var string
     */
    private string $prefixKeyTo;

    public function config(array|string $config): static
    {
        if (is_string($config)) {
            $this->config = json_decode($config, true);
        } elseif (is_array($config)) {
            $this->config = $config;
        }

        return $this;
    }

    /**
     * Set prefix key from
     * 
     * @param string $prefix
     * @return static
     */
    public function prefixKeyFrom(string $prefix): static
    {
        $this->prefixKeyFrom = $prefix . $this->arrayKeySeparator;
        return $this;
    }

    /**
     * Set prefix key to
     * 
     * @param string $prefix
     * @return static
     */
    public function prefixKeyTo(string $prefix): static
    {
        $this->prefixKeyTo = $prefix . $this->arrayKeySeparator;
        return $this;
    }

    public function transform(string $from, string $to): array
    {
        $this->fromApp = $from;
        $this->toApp = $to;
        foreach ($this->data as $dt) {
            $result = [];
            foreach ($this->config as $field) {
                $toConfig = $field[$to];
                if (!$this->isFieldAllowed($toConfig)) continue;

                $fromConfig = $field[$from];
                $value = $this->getValueAppFrom($dt, $fromConfig, $toConfig);
                $result = $this->setValueToResult($result, $value, $toConfig);
            }
            $this->result[] = $result;
        }

        return $this->result;
    }

    /**
     * Get value from app origin
     * 
     * @param array $data
     * @param array $fromConfig
     * @param array $toConfig
     * @return mixed
     */
    private function getValueAppFrom(array $data, array $fromConfig, array $toConfig): mixed
    {
        $keys = explode($this->arrayKeySeparator, str_replace($this->prefixKeyFrom, '', $fromConfig['key']));
        $value = &$data;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                $value = null;
                break;
            }
            $value = $value[$k];
        }

        if ($value === null || $value == '') {
            if (
                isset($toConfig['required']) &&
                $toConfig['required'] == 'true' &&
                isset($toConfig['default'])
            ) {
                if ($toConfig['type'] == 'date') {
                    $value = date($toConfig['format']);
                    // } elseif ($toConfig['type'] == 'number') {
                    //     $value = $toConfig['default'];
                } else {
                    $value = $toConfig['default'] == 'null' ? null : $toConfig['default'];
                }
            }
        }

        if ($value !== null && $value !== '') {
            $value = $this->transformValue($value, $toConfig);
        }

        return $value;
    }

    /**
     * Set value to result
     * 
     * @param array $result
     * @param mixed $value
     * @param array $config
     * @return array
     */
    private function setValueToResult(array $result, $value, array $config): array
    {
        if ($this->fillType->is(FillType::SKIP_NULL) && $value === null) return $result;
        if ($this->fillType->is(FillType::SKIP_EMPTY) && $value === '') return $result;
        if ($this->fillType->is(FillType::SKIP_NULL_EMPTY) && ($value === null || $value == '')) return $result;

        $keys = explode($this->arrayKeySeparator, str_replace($this->prefixKeyTo, '', $config['key']));
        $temp = &$result;

        foreach ($keys as $k) {
            if (!isset($temp[$k])) {
                $temp[$k] = [];
            }
            $temp = &$temp[$k];
        }
        $temp = $value;
        unset($temp);

        return $result;
    }
}
