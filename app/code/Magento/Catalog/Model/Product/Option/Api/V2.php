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
     * Api data
     *
     * @var Magento_Api_Helper_Data
     */
    protected $_apiData = null;

    /**
     * @param Magento_Core_Model_Event_Manager_Proxy $eventManager
     * @param Magento_Catalog_Helper_Product $catalogProduct
     * @param Magento_Api_Helper_Data $apiData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Controller_Request_Http $request
     */
    public function __construct(
        Magento_Core_Model_Event_Manager_Proxy $eventManager,
        Magento_Catalog_Helper_Product $catalogProduct,
        Magento_Api_Helper_Data $apiData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Controller_Request_Http $request
    ) {
        $this->_apiData = $apiData;
        parent::__construct($eventManager, $catalogProduct, $catalogData, $request);
    }

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
        $this->_apiData->toArray($data);
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
        $this->_apiData->toArray($data);
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
            $result[$key] = $this->_apiData->wsiArrayPacker($option);
        }
        return $result;
    }

}
