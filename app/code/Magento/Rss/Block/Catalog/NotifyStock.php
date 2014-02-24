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
 * Catalog low stock RSS block
 */
namespace Magento\Rss\Block\Catalog;

class NotifyStock extends \Magento\Backend\Block\AbstractBlock
{
    /**
     * @var \Magento\Rss\Model\RssFactory
     */
    protected $_rssFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\CatalogInventory\Model\Resource\StockFactory
     */
    protected $_stockFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_productStatus;

    /**
     * @var \Magento\Core\Model\Resource\Iterator
     */
    protected $_resourceIterator;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Rss\Model\RssFactory $rssFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\CatalogInventory\Model\Resource\StockFactory $stockFactory
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus
     * @param \Magento\Core\Model\Resource\Iterator $resourceIterator
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Rss\Model\RssFactory $rssFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\CatalogInventory\Model\Resource\StockFactory $stockFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Core\Model\Resource\Iterator $resourceIterator,
        array $data = array()
    ) {
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
        $newUrl = $this->getUrl('rss/catalog/notifystock', array('_secure' => true, '_nosecret' => true));
        $title = __('Low Stock Products');
        /** @var $rssObj \Magento\Rss\Model\Rss */
        $rssObj = $this->_rssFactory->create();
        $rssObj->_addHeader(array(
            'title'       => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
        ));

        $globalNotifyStockQty = (float)$this->_storeConfig->getConfig(
            \Magento\CatalogInventory\Model\Stock\Item::XML_PATH_NOTIFY_STOCK_QTY
        );
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->_productFactory->create();
        /* @var $collection \Magento\Catalog\Model\Resource\Product\Collection */
        $collection = $product->getCollection();
        /** @var $resourceStock \Magento\CatalogInventory\Model\Resource\Stock */
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
        /* @var $product \Magento\Catalog\Model\Product */
        $product = $args['product'];
        $product->setData($args['row']);
        $url = $this->getUrl('catalog/product/edit',
            array('id' => $product->getId(), '_secure' => true, '_nosecret' => true));
        $qty = 1 * $product->getQty();
        $description = __('%1 has reached a quantity of %2.', $product->getName(), $qty);
        /** @var $rssObj \Magento\Rss\Model\Rss */
        $rssObj = $args['rssObj'];
        $rssObj->_addEntry(array(
            'title'       => $product->getName(),
            'link'        => $url,
            'description' => $description,
        ));
    }
}
