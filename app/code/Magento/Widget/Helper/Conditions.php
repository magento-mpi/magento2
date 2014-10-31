<?php
namespace Magento\Widget\Helper;

/**
 * Widget Conditions helper
 */
class Conditions
{
    /**
     * Encode widget conditions to be used with WYSIWIG
     *
     * @param array $value
     * @return string
     */
    public function encode(array $value)
    {
        foreach ($value as &$condition) {
            if (isset($condition['type'])) {
                $condition['type'] = preg_quote($condition['type']);
            }
        }
        $value = str_replace(['{','}','"'],['[',']','`'], json_encode($value));
        return $value;
    }

    /**
     * Decode previously encoded widget conditions
     *
     * @param string $value
     * @return array
     */
    public function decode($value)
    {
        $value = str_replace(['[',']','`'],['{','}','"'], $value);
        return json_decode($value, true);
    }
}
