<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Convert;

use Magento\Exception;

/**
 * Convert the array data to SimpleXMLElement object
 */
class ConvertArray
{
    /**
     * Transform an assoc array to \SimpleXMLElement object
     * Array has some limitations. Appropriate exceptions will be thrown
     *
     * @param array $array
     * @param string $rootName
     * @return \SimpleXMLElement
     * @throws Exception
     */
    public function assocToXml(array $array, $rootName = '_')
    {
        if (empty($rootName) || is_numeric($rootName)) {
            throw new Exception('Root element must not be empty or numeric');
        }

        $xmlStr = <<<XML
<?xml version='1.0' encoding='UTF-8' standalone='yes'?>
<$rootName></$rootName>
XML;
        $xml = new \SimpleXMLElement($xmlStr);
        foreach (array_keys($array) as $key) {
            if (is_numeric($key)) {
                throw new Exception('Array root keys must not be numeric.');
            }
        }
        return self::_assocToXml($array, $rootName, $xml);
    }

    /**
     * Function, that actually recursively transforms array to xml
     *
     * @param array $array
     * @param string $rootName
     * @param \SimpleXMLElement $xml
     * @return \SimpleXMLElement
     * @throws Exception
     */
    private function _assocToXml(array $array, $rootName, \SimpleXMLElement &$xml)
    {
        $hasNumericKey = false;
        $hasStringKey  = false;
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                if (is_string($key)) {
                    if ($key === $rootName) {
                        throw new Exception(
                            'Associative key must not be the same as its parent associative key.'
                        );
                    }
                    $hasStringKey = true;
                    $xml->$key = $value;
                } elseif (is_int($key)) {
                    $hasNumericKey = true;
                    $xml->{$rootName}[$key] = $value;
                }
            } else {
                self::_assocToXml($value, $key, $xml->$key);
            }
        }
        if ($hasNumericKey && $hasStringKey) {
            throw new Exception('Associative and numeric keys must not be mixed at one level.');
        }
        return $xml;
    }
}
