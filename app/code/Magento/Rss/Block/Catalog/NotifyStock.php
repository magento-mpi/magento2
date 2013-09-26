<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review form block
 */
class Magento_Rss_Block_Catalog_NotifyStock extends Magento_Core_Block_Abstract
{
    /**
     * Rss data
     *
     * @var Magento_Rss_Helper_Data
     */
    protected $_rssData;

    /**
     * Adminhtml data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_adminhtmlData;

    /**
     * @var Magento_Rss_Model_RssFactory
     */
    protected $_rssFactory;

    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Magento_CatalogInventory_Model_Resource_StockFactory
     */
    protected $_stockFactory;

    /**
     * @var Magento_Catalog_Model_Product_Status
     */
    protected $_productStatus;

    /**
     * @var Magento_Core_Model_Resource_Iterator
     */
    protected $_resourceIterator;

    /**
     * @param Magento_Backend_Helper_Data $adminhtmlData
     * @param Magento_Rss_Helper_Data $rssData
     * @param Magento_Core_Block_Context $context
     * @param Magento_Rss_Model_RssFactory $rssFactory
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_CatalogInventory_Model_Resource_StockFactory $stockFactory
     * @param Magento_Catalog_Model_Product_Status $productStatus
     * @param Magento_Core_Model_Resource_Iterator $resourceIterator
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Helper_Data $adminhtmlData,
        Magento_Rss_Helper_Data $rssData,
        Magento_Core_Block_Context $context,
        Magento_Rss_Model_RssFactory $rssFactory,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_CatalogInventory_Model_Resource_StockFactory $stockFactory,
        Magento_Catalog_Model_Product_Status $productStatus,
        Magento_Core_Model_Resource_Iterator $resourceIterator,
        array $data = array()
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        $this->_rssData = $rssData;
        $this->_rssFactory = $rssFactory;
        $this->_productFactory = $productFactory;
        $this->_stockFactory = $stockFactory;
        $this->_productStatus = $productStatus;
        $this->_resourceIterator = $resourceIterator;
        parent::__construct($context, $data);
    }

    /**
     * Render RSS
     *
     * @return string
     */
    protected function _toHtml()
    {
        $newUrl = $this->_urlBuilder->getUrl('rss/catalog/notifystock');
        $title = __('Low Stock Products');
        /** @var $rssObj Magento_Rss_Model_Rss */
        $rssObj = $this->_rssFactory->create();
        $rssObj->_addHeader(array(
            'title'       => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
        ));

        $globalNotifyStockQty = (float)$this->_storeConfig->getConfig(
            Magento_CatalogInventory_Model_Stock_Item::XML_PATH_NOTIFY_STOCK_QTY
        );
        $this->_rssData->disableFlat();
        /* @var $product Magento_Catalog_Model_Product */
        $product = $this->_productFactory->create();
        /* @var $collection Magento_Catalog_Model_Resource_Product_Collection */
        $collection = $product->getCollection();
        /** @var $resourceStock Magento_CatalogInventory_Model_Resource_Stock */
        $resourceStock = $this->_stockFactory->create();
        $resourceStock->addLowStockFilter($collection, array(
            'qty',
            'notify_stock_qty',
            'low_stock_date',
            'use_config' => 'use_config_notify_stock_qty'
        ));
        $collection->addAttributeToSelect('name', true)
            ->addAttributeToFilter('status', array('in' => $this->_productStatus->getVisibleStatusIds()))
            ->setOrder('low_stock_date');
        $this->_eventManager->dispatch('rss_catalog_notify_stock_collection_select', array(
            'collection' => $collection,
        ));

        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        $this->_resourceIterator->walk(
            $collection->getSelect(),
            array(array($this, 'addNotifyItemXmlCallback')),
            array('rssObj' => $rssObj, 'product' => $product, 'globalQty' => $globalNotifyStockQty)
        );

        return $rssObj->createRssXml();
    }

    /**
     * Adds single product to feed
     *
     * @param array $args
     * @return void
     */
    public function addNotifyItemXmlCallback($args)
    {
        /* @var $product Magento_Catalog_Model_Product */
        $product = $args['product'];
        $product->setData($args['row']);
        $url = $this->_adminhtmlData->getUrl('adminhtml/catalog_product/edit/',
            array('id' => $product->getId(), '_secure' => true, '_nosecret' => true));
        $qty = 1 * $product->getQty();
        $description = __('%1 has reached a quantity of %2.', $product->getName(), $qty);
        /** @var $rssObj Magento_Rss_Model_Rss */
        $rssObj = $args['rssObj'];
        $rssObj->_addEntry(array(
            'title'       => $product->getName(),
            'link'        => $url,
            'description' => $description,
        ));
    }
}
