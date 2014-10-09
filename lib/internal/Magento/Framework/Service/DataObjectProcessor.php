<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service;

use Zend\Code\Reflection\ClassReflection;

class DataObjectProcessor
{
    /**
     * Use class reflection on given data interface to build output data array
     *
     * @param mixed $dataObject
     * @param string $dataObjectType
     * @return array
     */
    public function buildOutputDataArray($dataObject, $dataObjectType)
    {
        $class = new ClassReflection($dataObjectType);
        $methods = $class->getMethods();

        $outputData = [];
        foreach ($methods as $method) {
            if ($method->getNumberOfParameters() > 0) {
                continue;
            }
            if (substr($method->getName(), 0, 2) === 'is') {
                $outputData[$this->_fieldNameConverter(substr($method->getName(), 2))]
                    = $dataObject->{$method->getName()}();

            } elseif (substr($method->getName(), 0, 3) === 'get') {
                $outputData[$this->_fieldNameConverter(substr($method->getName(), 3))]
                    = $dataObject->{$method->getName()}();
            }
        }
        return $outputData;
    }

    /**
     * Converts field names to use lowercase
     *
     * @param string $name
     * @return string
     */
    protected function _fieldNameConverter($name)
    {
        $result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
        return $result;
    }
}
