<?php

namespace Tranquangkhuong\DataTransformer;

use Tranquangkhuong\DataTransformer\Abstract\Transformer;
use Tranquangkhuong\DataTransformer\Enum\FillType;

/**
 * Transform data
 */
class DataTransformer extends Transformer
{
    public function config(array|string $config): static
    {
        if (is_string($config)) {
            $conf = json_decode($config, true);
        } elseif (is_array($config)) {
            $conf = $config;
        }
        $this->config = $conf['configs'];
        if (isset($conf['variables'])) {
            if (
                isset($conf['variables']['array_key_separator']) &&
                (string) $conf['variables']['array_key_separator'] != ''
            ) {
                $this->arrayKeySeparator($conf['variables']['array_key_separator']);
            }
            if (
                isset($conf['variables']['group_value_separator']) &&
                (string) $conf['variables']['group_value_separator'] != ''
            ) {
                $this->groupValueSeparator($conf['variables']['group_value_separator']);
            }
        }

        return $this;
    }

    public function transform(string $from, string $to): array
    {
        $this->fromApp = $from;
        $this->toApp = $to;
        foreach ($this->config as $field) {
            $toConfig = $field[$to];
            if (!$this->isFieldAllowed($toConfig)) continue;
            $fromConfig = $field[$from];
            $value = $this->getValueAppFrom($fromConfig, $toConfig);
            $this->setValueToResult($value, $toConfig);
        }

        return $this->result;
    }

    /**
     * Get value from app origin
     * 
     * @param array $fromConfig
     * @param array $toConfig
     * @return mixed
     */
    private function getValueAppFrom(array $fromConfig, array $toConfig): mixed
    {
        $keys = explode($this->arrayKeySeparator, $fromConfig['key']);
        $value = $this->data;

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

        if (isset($toConfig['multiple']) && $toConfig['multiple'] == 'true') {
            $value = (new MultipleChildValue)->config($toConfig['children'])
                ->data($value)
                ->case($this->case)
                ->prefixKeyFrom($fromConfig['key'])
                ->prefixKeyTo($toConfig['key'])
                ->transform($this->fromApp, $this->toApp);
        } else if ($value !== null && $value !== '') {
            $value = $this->transformValue($value, $toConfig);
        }

        return $value;
    }

    /**
     * Set value to result
     * 
     * @param mixed $value
     * @param array $config
     * @return void
     */
    private function setValueToResult(mixed $value, array $config): void
    {
        if ($this->fillType->is(FillType::SKIP_NULL) && $value === null) return;
        if ($this->fillType->is(FillType::SKIP_EMPTY) && $value === '') return;
        if ($this->fillType->is(FillType::SKIP_NULL_EMPTY) && ($value === null || $value == '')) return;

        $keys = explode($this->arrayKeySeparator, $config['key']);
        $temp = &$this->result;

        foreach ($keys as $k) {
            if (!isset($temp[$k])) {
                $temp[$k] = [];
            }
            $temp = &$temp[$k];
        }
        $temp = $value;
        unset($temp);
    }
}
