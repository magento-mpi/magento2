<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
if (!function_exists('array_replace_recursive')) {
    function array_replace_recursive()
    {
        $args = func_get_args();
        $result = $args[0];
        if (!is_array($result)) {
            return $result;
        }
        for ($i = 1; $i < count($args); $i++) {
            if (!is_array($args[$i])) {
                continue;
            }
            foreach ($args[$i] as $key => $value) {
                if (!isset($result[$key]) || (isset($result[$key]) && !is_array($result[$key]))) {
                    $result[$key] = array();
                }
                if (is_array($value)) {
                    $value = array_replace_recursive($result[$key], $value);
                }
                $result[$key] = $value;
            }
        }
        return $result;
    }
}
function array_diff_recursive($array1, $array2)
{
    $aReturn = array();
    foreach ($array1 as $mKey => $mValue) {
        if (array_key_exists($mKey, $array2)) {
            if (is_array($mValue)) {
                $aRecursiveDiff = array_diff_recursive($mValue, $array2[$mKey]);
                if (count($aRecursiveDiff)) {
                    $aReturn[$mKey] = $aRecursiveDiff;
                }
            } else {
                if ($mValue != $array2[$mKey]) {
                    $aReturn[$mKey] = $mValue;
                }
            }
        } else {
            $aReturn[$mKey] = $mValue;
        }
    }
    return $aReturn;
}