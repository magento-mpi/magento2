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
    /**
     * @var Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    protected $_designCollection;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Gift wrapping data
     *
     * @var Magento_GiftWrapping_Helper_Data
     */
    protected $_giftWrappingData = null;

    /**
     * @var Magento_GiftWrapping_Model_Resource_Wrapping_CollectionFactory
     */
    protected $_wrappingCollFactory;

    /**
     * Store list manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_GiftWrapping_Helper_Data $giftWrappingData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_GiftWrapping_Model_Resource_Wrapping_CollectionFactory $wrappingCollFactory
     * @param array $data
     */
    public function __construct(
        Magento_GiftWrapping_Helper_Data $giftWrappingData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_GiftWrapping_Model_Resource_Wrapping_CollectionFactory $wrappingCollFactory,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_giftWrappingData = $giftWrappingData;
        $this->_storeManager = $storeManager;
        $this->_wrappingCollFactory = $wrappingCollFactory;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve order model instance
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('sales_order');
    }

    /**
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
            $store = $this->_storeManager->getStore($this->getStoreId());
            $this->_designCollection = $this->_wrappingCollFactory->create()
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
