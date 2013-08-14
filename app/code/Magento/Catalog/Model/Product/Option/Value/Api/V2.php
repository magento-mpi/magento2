<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product option values api
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Option_Value_Api_V2 extends Magento_Catalog_Model_Product_Option_Value_Api
{
    /**
     * Retrieve values from specified option
     *
     * @param string $optionId
     * @param int|string|null $store
     * @return array
     */
    public function items($optionId, $store = null)
    {
        $result = parent::items($optionId, $store);
        foreach ($result as $key => $optionValue) {
            $result[$key] = Mage::helper('Magento_Api_Helper_Data')->wsiArrayPacker($optionValue);
        }
        return $result;
    }

    /**
     * Retrieve specified option value info
     *
     * @param string $valueId
     * @param int|string|null $store
     * @return array
     */
    public function info($valueId, $store = null)
    {
        return Mage::helper('Magento_Api_Helper_Data')->wsiArrayPacker(
            parent::info($valueId, $store)
        );
    }

    /**
     * Add new values to select option
     *
     * @param string $optionId
     * @param array $data
     * @param int|string|null $store
     * @return bool
     */
    public function add($optionId, $data, $store = null)
    {
        Mage::helper('Magento_Api_Helper_Data')->toArray($data);
        return parent::add($optionId, $data, $store);
    }

    /**
     * Update value to select option
     *
     * @param string $valueId
     * @param array $data
     * @param int|string|null $store
     * @return bool
     */
    public function update($valueId, $data, $store = null)
    {
        Mage::helper('Magento_Api_Helper_Data')->toArray($data);
        return parent::update($valueId, $data, $store);
    }

    /**
     * Delete value from select option
     *
     * @param int $valueId
     * @return boolean
     */
    public function remove($valueId)
    {
        return parent::remove($valueId);
    }
}
