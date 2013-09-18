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
 *
 * @category   Magento
 * @package    Magento_Rss
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rss_Block_Catalog_NotifyStock extends Magento_Core_Block_Abstract
{
    /**
     * Rss data
     *
     * @var Magento_Rss_Helper_Data
     */
    protected $_rssData = null;

    /**
     * Adminhtml data
     *
     * @var Magento_Backend_Helper_Data
     */
    protected $_adminhtmlData = null;

    /**
     * @var Magento_CatalogInventory_Model_Resource_Stock
     */
    protected $_stockResource;

    /**
     * @var Magento_Catalog_Model_Product_Status
     */
    protected $_productStatus;

    /**
     * @var Magento_Core_Model_Resource_Iterator
     */
    protected $_iterator;

    /**
     * @param Magento_CatalogInventory_Model_Resource_Stock $stockResource
     * @param Magento_Catalog_Model_Product_Status $productStatus
     * @param Magento_Core_Model_Resource_Iterator $iterator
     * @param Magento_Backend_Helper_Data $adminhtmlData
     * @param Magento_Rss_Helper_Data $rssData
     * @param Magento_Core_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_CatalogInventory_Model_Resource_Stock $stockResource,
        Magento_Catalog_Model_Product_Status $productStatus,
        Magento_Core_Model_Resource_Iterator $iterator,
        Magento_Backend_Helper_Data $adminhtmlData,
        Magento_Rss_Helper_Data $rssData,
        Magento_Core_Block_Context $context,
        array $data = array()
    ) {
        $this->_stockResource = $stockResource;
        $this->_productStatus = $productStatus;
        $this->_iterator = $iterator;
        $this->_adminhtmlData = $adminhtmlData;
        $this->_rssData = $rssData;
        parent::__construct($context, $data);
    }

    /**
     * Render RSS
     *
     * @return string
     */
    protected function _toHtml()
    {
        $newUrl = Mage::getUrl('rss/catalog/notifystock');
        $title = __('Low Stock Products');

        $rssObj = Mage::getModel('Magento_Rss_Model_Rss');
        $data = array(
            'title'       => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
        );
        $rssObj->_addHeader($data);

        $globalNotifyStockQty = (float) Mage::getStoreConfig(
            Magento_CatalogInventory_Model_Stock_Item::XML_PATH_NOTIFY_STOCK_QTY);
        $this->_rssData->disableFlat();
        /* @var $product Magento_Catalog_Model_Product */
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        /* @var $collection Magento_Catalog_Model_Resource_Product_Collection */
        $collection = $product->getCollection();
        $this->_stockResource->addLowStockFilter($collection, array(
            'qty',
            'notify_stock_qty',
            'low_stock_date',
            'use_config' => 'use_config_notify_stock_qty'
        ));
        $collection
            ->addAttributeToSelect('name', true)
            ->addAttributeToFilter('status',
                array('in' => $this->_productStatus->getVisibleStatusIds())
            )
            ->setOrder('low_stock_date');
        $this->_eventManager->dispatch('rss_catalog_notify_stock_collection_select', array(
            'collection' => $collection,
        ));

        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        $this->_iterator->walk(
            $collection->getSelect(),
            array(array($this, 'addNotifyItemXmlCallback')),
            array('rssObj'=> $rssObj, 'product'=>$product, 'globalQty' => $globalNotifyStockQty)
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
        $product = $args['product'];
        $product->setData($args['row']);
        $url = $this->_adminhtmlData->getUrl('adminhtml/catalog_product/edit/',
            array('id' => $product->getId(), '_secure' => true, '_nosecret' => true));
        $qty = 1 * $product->getQty();
        $description = __('%1 has reached a quantity of %2.', $product->getName(), $qty);
        $rssObj = $args['rssObj'];
        $data = array(
            'title'         => $product->getName(),
            'link'          => $url,
            'description'   => $description,
        );
        $rssObj->_addEntry($data);
    }
}
