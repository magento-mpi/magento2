<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product Tag API
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Model_Api_V2 extends Magento_Tag_Model_Api
{
    /**
     * Retrieve list of tags for specified product as array of objects
     *
     * @param int $productId
     * @param string|int $store
     * @return array
     */
    public function items($productId, $store = null)
    {
        $result = parent::items($productId, $store);
        foreach ($result as $key => $tag) {
            $result[$key] = Mage::helper('Magento_Api_Helper_Data')->wsiArrayPacker($tag);
        }
        return array_values($result);
    }

    /**
     * Add tag(s) to product.
     * Return array of objects
     *
     * @param array $data
     * @return array
     */
    public function add($data)
    {
        $result = array();
        foreach (parent::add($data) as $key => $value) {
            $result[] = array('key' => $key, 'value' => $value);
        }

        return $result;
    }

    /**
     * Retrieve tag info as object
     *
     * @param int $tagId
     * @param string|int $store
     * @return object
     */
    public function info($tagId, $store)
    {
        $result = parent::info($tagId, $store);
        $result = Mage::helper('Magento_Api_Helper_Data')->wsiArrayPacker($result);
        foreach ($result->products as $key => $value) {
            $result->products[$key] = array('key' => $key, 'value' => $value);
        }
        return $result;
    }

    /**
     * Convert data from object to array before add
     *
     * @param object $data
     * @return array
     */
    protected function _prepareDataForAdd($data)
    {
        Mage::helper('Magento_Api_Helper_Data')->toArray($data);
        return parent::_prepareDataForAdd($data);
    }

    /**
     * Convert data from object to array before update
     *
     * @param object $data
     * @return array
     */
    protected function _prepareDataForUpdate($data)
    {
        Mage::helper('Magento_Api_Helper_Data')->toArray($data);
        return parent::_prepareDataForUpdate($data);
    }
}
