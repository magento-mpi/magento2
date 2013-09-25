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
 * Catalog product related items block
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Magento_Catalog_Block_Product_List_Upsell extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    protected $_columnCount = 4;

    protected $_items;

    protected $_itemCollection;

    protected $_itemLimits = array();

    /**
     * Checkout session
     *
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * Catalog product visibility
     *
     * @var Magento_Catalog_Model_Product_Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Checkout cart
     *
     * @var Magento_Checkout_Model_Resource_Cart
     */
    protected $_checkoutCart;

    /**
     * Construct
     *
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Checkout_Model_Resource_Cart $checkoutCart
     * @param Magento_Catalog_Model_Product_Visibility $catalogProductVisibility
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Catalog_Model_Config $catalogConfig
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Checkout_Model_Resource_Cart $checkoutCart,
        Magento_Catalog_Model_Product_Visibility $catalogProductVisibility,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Catalog_Model_Config $catalogConfig,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_checkoutCart = $checkoutCart;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($storeManager, $catalogConfig, $coreRegistry, $taxData, $catalogData, $coreData,
            $context, $data);
    }

    protected function _prepareData()
    {
        $product = $this->_coreRegistry->registry('product');
        /* @var $product Magento_Catalog_Model_Product */
        $this->_itemCollection = $product->getUpSellProductCollection()
            ->setPositionOrder()
            ->addStoreFilter();
        if ($this->_catalogData->isModuleEnabled('Magento_Checkout')) {
            $this->_checkoutCart->addExcludeProductFilter(
                $this->_itemCollection,
                $this->_checkoutSession->getQuoteId()
            );

            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
        $this->_itemCollection->setVisibility(
            $this->_catalogProductVisibility->getVisibleInCatalogIds()
        );

        if ($this->getItemLimit('upsell') > 0) {
            $this->_itemCollection->setPageSize($this->getItemLimit('upsell'));
        }

        $this->_itemCollection->load();

        /**
         * Updating collection with desired items
         */
        $this->_eventManager->dispatch('catalog_product_upsell', array(
            'product'       => $product,
            'collection'    => $this->_itemCollection,
            'limit'         => $this->getItemLimit()
        ));

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    public function getItemCollection()
    {
        return $this->_itemCollection;
    }

    public function getItems()
    {
        if (is_null($this->_items)) {
            $this->_items = $this->getItemCollection()->getItems();
        }
        return $this->_items;
    }

    public function getRowCount()
    {
        return ceil(count($this->getItemCollection()->getItems())/$this->getColumnCount());
    }

    public function setColumnCount($columns)
    {
        if (intval($columns) > 0) {
            $this->_columnCount = intval($columns);
        }
        return $this;
    }

    public function getColumnCount()
    {
        return $this->_columnCount;
    }

    public function resetItemsIterator()
    {
        $this->getItems();
        reset($this->_items);
    }

    public function getIterableItem()
    {
        $item = current($this->_items);
        next($this->_items);
        return $item;
    }

    /**
     * Set how many items we need to show in upsell block
     * Notice: this parametr will be also applied
     *
     * @param string $type
     * @param int $limit
     * @return Magento_Catalog_Block_Product_List_Upsell
     */
    public function setItemLimit($type, $limit)
    {
        if (intval($limit) > 0) {
            $this->_itemLimits[$type] = intval($limit);
        }
        return $this;
    }

    public function getItemLimit($type = '')
    {
        if ($type == '') {
            return $this->_itemLimits;
        }
        if (isset($this->_itemLimits[$type])) {
            return $this->_itemLimits[$type];
        } else {
            return 0;
        }
    }
}
