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
 * Catalog product options api
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Option_Api_V2 extends Magento_Catalog_Model_Product_Option_Api
{

    /**
     * Add custom option to product
     *
     * @param string $productId
     * @param array $data
     * @param int|string|null $store
     * @return bool
     */
    public function add($productId, $data, $store = null)
    {
        Mage::helper('Magento_Api_Helper_Data')->toArray($data);
        return parent::add($productId, $data, $store);
    }

    /**
     * Update product custom option data
     *
     * @param string $optionId
     * @param array $data
     * @param int|string|null $store
     * @return bool
     */
    public function update($optionId, $data, $store = null)
    {
        Mage::helper('Magento_Api_Helper_Data')->toArray($data);
        return parent::update($optionId, $data, $store);
    }

    /**
     * Retrieve list of product custom options
     *
     * @param string $productId
     * @param int|string|null $store
     * @return array
     */
    public function items($productId, $store = null)
    {
        $result = parent::items($productId, $store);
        foreach ($result as $key => $option) {
            $result[$key] = Mage::helper('Magento_Api_Helper_Data')->wsiArrayPacker($option);
        }
        return $result;
    }

}
