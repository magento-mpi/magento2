<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping order view abstract block
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftWrapping_Block_Adminhtml_Order_View_Abstract extends Magento_Core_Block_Template
{
    protected $_designCollection;

    /**
     * Gift wrapping data
     *
     * @var Magento_GiftWrapping_Helper_Data
     */
    protected $_giftWrappingData = null;

    /**
     * @param Magento_GiftWrapping_Helper_Data $giftWrappingData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_GiftWrapping_Helper_Data $giftWrappingData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_giftWrappingData = $giftWrappingData;
        parent::__construct($coreData, $context, $data);
    }

    /*
     * Retrieve order model instance
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('sales_order');
    }

    /*
     * Get store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getOrder()->getStoreId();
    }

    /**
     * Gift wrapping collection
     *
     * @return Magento_GiftWrapping_Model_Resource_Wrapping_Collection
     */
    public function getDesignCollection()
    {
        if (is_null($this->_designCollection)) {
            $store = Mage::app()->getStore($this->getStoreId());
            $this->_designCollection = Mage::getModel('Magento_GiftWrapping_Model_Wrapping')->getCollection()
                ->addStoreAttributesToResult($store->getId())
                ->applyStatusFilter()
                ->applyWebsiteFilter($store->getWebsiteId());
        }
        return $this->_designCollection;
    }

    /**
     * Return gift wrapping designs info
     *
     * @return Magento_Object
     */
    public function getDesignsInfo()
    {
        $data = array();
        foreach ($this->getDesignCollection()->getItems() as $item) {
            $temp['path'] = $item->getImageUrl();
            $temp['design'] = $this->escapeHtml($item->getDesign());
            $data[$item->getId()] = $temp;
        }
        return new Magento_Object($data);
    }

    /**
     * Prepare prices for display
     * @param float $basePrice
     * @param float $price
     * @return string
     */
    protected function _preparePrices($basePrice, $price)
    {
        return $this->helper('Magento_Adminhtml_Helper_Sales')->displayPrices($this->getOrder(), $basePrice, $price);
    }

    /**
     * Check ability to display both prices for gift wrapping in backend sales
     *
     * @return bool
     */
    public function getDisplayWrappingBothPrices()
    {
        return $this->_giftWrappingData->displaySalesWrappingBothPrices();
    }

    /**
     * Check ability to display prices including tax for gift wrapping in backend sales
     *
     * @return bool
     */
    public function getDisplayWrappingPriceInclTax()
    {
        return $this->_giftWrappingData->displaySalesWrappingIncludeTaxPrice();
    }

    /**
     * Check ability to display both prices for printed card in backend sales
     *
     * @return bool
     */
    public function getDisplayCardBothPrices()
    {
        return $this->_giftWrappingData->displaySalesCardBothPrices();
    }

    /**
     * Check ability to display prices including tax for printed card in backend sales
     *
     * @return bool
     */
    public function getDisplayCardPriceInclTax()
    {
        return $this->_giftWrappingData->displaySalesCardIncludeTaxPrice();
    }
}
