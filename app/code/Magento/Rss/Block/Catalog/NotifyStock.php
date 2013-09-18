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
namespace Magento\Rss\Block\Catalog;

class NotifyStock extends \Magento\Core\Block\AbstractBlock
{
    /**
     * Rss data
     *
     * @var \Magento\Rss\Helper\Data
     */
    protected $_rssData = null;

    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_adminhtmlData = null;

    /**
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     * @param \Magento\Rss\Helper\Data $rssData
     * @param \Magento\Core\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Helper\Data $adminhtmlData,
        \Magento\Rss\Helper\Data $rssData,
        \Magento\Core\Block\Context $context,
        array $data = array()
    ) {
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
        $newUrl = \Mage::getUrl('rss/catalog/notifystock');
        $title = __('Low Stock Products');

        $rssObj = \Mage::getModel('Magento\Rss\Model\Rss');
        $data = array(
            'title'       => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
        );
        $rssObj->_addHeader($data);

        $globalNotifyStockQty = (float) $this->_storeConfig->getConfig(
            Magento_CatalogInventory_Model_Stock_Item::XML_PATH_NOTIFY_STOCK_QTY);
        $this->_rssData->disableFlat();
        /* @var $product \Magento\Catalog\Model\Product */
        $product = \Mage::getModel('Magento\Catalog\Model\Product');
        /* @var $collection \Magento\Catalog\Model\Resource\Product\Collection */
        $collection = $product->getCollection();
        \Mage::getResourceModel('Magento\CatalogInventory\Model\Resource\Stock')->addLowStockFilter($collection, array(
            'qty',
            'notify_stock_qty',
            'low_stock_date',
            'use_config' => 'use_config_notify_stock_qty'
        ));
        $collection
            ->addAttributeToSelect('name', true)
            ->addAttributeToFilter('status',
                array('in' => \Mage::getSingleton('Magento\Catalog\Model\Product\Status')->getVisibleStatusIds())
            )
            ->setOrder('low_stock_date');
        $this->_eventManager->dispatch('rss_catalog_notify_stock_collection_select', array(
            'collection' => $collection,
        ));

        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        \Mage::getSingleton('Magento\Core\Model\Resource\Iterator')->walk(
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
