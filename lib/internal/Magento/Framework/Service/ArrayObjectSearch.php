<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Service;

class ArrayObjectSearch
{
    /**
     * Search for the value's value by specified key's name-value pair in the object
     *
     * @param object $data Object to search in
     * @param string $keyValue Value of the key property to search for
     * @param string $keyName Name of the key property to search for
     * @param string $valueName Name of the value property name
     * @return null | mixed
     */
    public static function getArrayElementByName($data, $keyValue, $keyName = 'name', $valueName = 'value')
    {
        $getter = 'get' . ucfirst($keyName);
        if (is_array($data)) {
            foreach ($data as $dataObject) {
                if (is_object($dataObject) && $dataObject->$getter() == $keyValue) {
                    $valueGetter = 'get' . ucfirst($valueName);
                    return $dataObject->$valueGetter();
                }
            }
        }
        return null;
    }
}
