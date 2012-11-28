<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Di_Definition_ArrayDefinition_Zend extends Zend\Di\Definition\ArrayDefinition
    implements Magento_Di_Definition_ArrayDefinition
{
    /**
     * @param array $dataArray
     */
    public function __construct(Array $dataArray)
    {
        $this->dataArray = $dataArray;
    }

    /**
     * Check whether definition contains class
     *
     * @param string $class
     * @return bool
     */
    public function hasClass($class)
    {
        $result = array_key_exists($class, $this->dataArray);
        if ($result && !is_array($this->dataArray[$class])) {
            $this->dataArray[$class] = json_decode($this->dataArray[$class], true);
        }
        return $result;
    }
}
