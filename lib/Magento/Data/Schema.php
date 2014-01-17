<?php
/**
 *
 * @category    Magento
 * @package     Magento_Data
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Data;

use Magento\Data\DataArray;

class Schema extends \Magento\Object
{
    /**
     * @param mixed $schema
     *
     * @return void
     */
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

    /**
     * @param mixed $rawData
     *
     * @return DataArray
     */
    public function extract($rawData)
    {
        $elements = $rawData;
        return new \Magento\Data\DataArray($elements);
    }
}
