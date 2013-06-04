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
            if (is_file($schema)) {
                include $schema;
                $this->setData($schema);
            }
        }
    }

    public function extract($rawData)
    {
        $elements = $rawData;
        return new Magento_Data_Array($elements);
    }
}
