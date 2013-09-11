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
     * Render RSS
     *
     * @return string
     */
    protected function _toHtml()
    {
        $newUrl = \Mage::getUrl('rss/catalog/notifystock');
        $title = __('Low Stock Products');

        $rssObj = \Mage::getModel('\Magento\Rss\Model\Rss');
        $data = array(
            'title'       => $title,
            'description' => $title,
            'link'        => $newUrl,
            'charset'     => 'UTF-8',
        );
        $rssObj->_addHeader($data);

        $globalNotifyStockQty = (float) \Mage::getStoreConfig(
            \Magento\CatalogInventory\Model\Stock\Item::XML_PATH_NOTIFY_STOCK_QTY);
        \Mage::helper('Magento\Rss\Helper\Data')->disableFlat();
        /* @var $product \Magento\Catalog\Model\Product */
        $product = \Mage::getModel('\Magento\Catalog\Model\Product');
        /* @var $collection \Magento\Catalog\Model\Resource\Product\Collection */
        $collection = $product->getCollection();
        \Mage::getResourceModel('\Magento\CatalogInventory\Model\Resource\Stock')->addLowStockFilter($collection, array(
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
        \Mage::dispatchEvent('rss_catalog_notify_stock_collection_select', array('collection' => $collection));

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
        $url = \Mage::helper('Magento\Adminhtml\Helper\Data')->getUrl('adminhtml/catalog_product/edit/',
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
