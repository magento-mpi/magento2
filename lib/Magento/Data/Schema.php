<?php
/**
 *
 * @category    Magento
 * @package     Magento_Data
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Data_Schema extends Varien_Object
{
    public function load($schema)
    {
        if (is_array($schema)) {
            $this->setData($schema);
        } elseif (is_string($schema)) {
            // @todo load schema file by file name reference
            throw new Exception('Need to be implemented: load schema file by file name reference in ' . __METHOD__);
        }
    }

    public function extract($rawData)
    {
        $elements = $rawData;
        return new Magento_Data_Array($elements);
    }
}
