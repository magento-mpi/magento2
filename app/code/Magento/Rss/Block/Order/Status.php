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
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rss\Block\Order;

class Status extends \Magento\Core\Block\Template
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_order_status_'.$this->getRequest()->getParam('data'));
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        $rssObj = \Mage::getModel('\Magento\Rss\Model\Rss');
        $order = \Mage::registry('current_order');
        if (!$order) {
            return '';
        }
        $title = __('Order # %1 Notification(s)', $order->getIncrementId());
        $newurl = \Mage::getUrl('sales/order/view',array('order_id' => $order->getId()));
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                );
        $rssObj->_addHeader($data);
        $resourceModel = \Mage::getResourceModel('\Magento\Rss\Model\Resource\Order');
        $results = $resourceModel->getAllCommentCollection($order->getId());
        if($results){
            foreach($results as $result){
                $urlAppend = 'view';
                $type = $result['entity_type_code'];
                if($type && $type!='order'){
                   $urlAppend = $type;
                }
                $type  = __(ucwords($type));
                $title = __('Details for %1 #%2', $type, $result['increment_id']);

                $description = '<p>'.
                __('Notified Date: %1<br/>',$this->formatDate($result['created_at'])).
                __('Comment: %1<br/>',$result['comment']).
                '</p>'
                ;
                $url = \Mage::getUrl('sales/order/'.$urlAppend,array('order_id' => $order->getId()));
                $data = array(
                    'title'         => $title,
                    'link'          => $url,
                    'description'   => $description,
                );
                $rssObj->_addEntry($data);
            }
        }
        $title = __('Order #%1 created at %2', $order->getIncrementId(), $this->formatDate($order->getCreatedAt()));
        $url = \Mage::getUrl('sales/order/view',array('order_id' => $order->getId()));
        $description = '<p>'.
            __('Current Status: %1<br/>',$order->getStatusLabel()).
            __('Total: %1<br/>',$order->formatPrice($order->getGrandTotal())).
            '</p>'
        ;
        $data = array(
                    'title'         => $title,
                    'link'          => $url,
                    'description'   => $description,
        );
        $rssObj->_addEntry($data);
        return $rssObj->createRssXml();
    }
}
