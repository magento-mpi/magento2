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
namespace Magento\Rss\Block\Order;

class New extends \Magento\Core\Block\AbstractBlock
{
    protected function _toHtml()
    {
        $order = \Mage::getModel('\Magento\Sales\Model\Order');
        $passDate = $order->getResource()->formatDate(mktime(0,0,0,date('m'),date('d')-7));

        $newurl = \Mage::helper('Magento\Adminhtml\Helper\Data')->getUrl(
            'adminhtml/sales_order',
            array(
                '_secure' => true,
                '_nosecret' => true
            )
        );
        $title = __('New Orders');

        $rssObj = \Mage::getModel('\Magento\Rss\Model\Rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                );
        $rssObj->_addHeader($data);

        $collection = $order->getCollection()
            ->addAttributeToFilter('created_at', array('date'=>true, 'from'=> $passDate))
            ->addAttributeToSort('created_at','desc')
        ;

        $detailBlock = \Mage::getBlockSingleton('\Magento\Rss\Block\Order\Details');

        \Mage::dispatchEvent('rss_order_new_collection_select', array('collection' => $collection));

        \Mage::getSingleton('Magento\Core\Model\Resource\Iterator')
            ->walk($collection->getSelect(), array(array($this, 'addNewOrderXmlCallback')), array('rssObj'=> $rssObj, 'order'=>$order , 'detailBlock' => $detailBlock));

        return $rssObj->createRssXml();
    }

    public function addNewOrderXmlCallback($args)
    {
        $rssObj = $args['rssObj'];
        $order = $args['order'];
        $detailBlock = $args['detailBlock'];
        $order->reset()->load($args['row']['entity_id']);
        if ($order && $order->getId()) {
            $title = __('Order #%1 created at %2', $order->getIncrementId(), $this->formatDate($order->getCreatedAt()));
            $url = \Mage::helper('Magento\Adminhtml\Helper\Data')->getUrl(
                'adminhtml/sales_order/view',
                array(
                    '_secure' => true,
                    'order_id' => $order->getId(),
                    '_nosecret' => true
                )
            );
            $detailBlock->setOrder($order);
            $data = array(
                    'title'         => $title,
                    'link'          => $url,
                    'description'   => $detailBlock->toHtml()
                    );
            $rssObj->_addEntry($data);
        }
    }
}
