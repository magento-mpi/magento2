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
 * @category   Mage
 * @package    Magento_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rss_Block_Order_Status extends Magento_Core_Block_Template
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
        $rssObj = Mage::getModel('Magento_Rss_Model_Rss');
        $order = Mage::registry('current_order');
        if (!$order) {
            return '';
        }
        $title = Mage::helper('Magento_Rss_Helper_Data')->__('Order # %s Notification(s)', $order->getIncrementId());
        $newurl = Mage::getUrl('sales/order/view',array('order_id' => $order->getId()));
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                );
        $rssObj->_addHeader($data);
        $resourceModel = Mage::getResourceModel('Magento_Rss_Model_Resource_Order');
        $results = $resourceModel->getAllCommentCollection($order->getId());
        if($results){
            foreach($results as $result){
                $urlAppend = 'view';
                $type = $result['entity_type_code'];
                if($type && $type!='order'){
                   $urlAppend = $type;
                }
                $type  = Mage::helper('Magento_Rss_Helper_Data')->__(ucwords($type));
                $title = Mage::helper('Magento_Rss_Helper_Data')->__('Details for %s #%s', $type, $result['increment_id']);

                $description = '<p>'.
                Mage::helper('Magento_Rss_Helper_Data')->__('Notified Date: %s<br/>',$this->formatDate($result['created_at'])).
                Mage::helper('Magento_Rss_Helper_Data')->__('Comment: %s<br/>',$result['comment']).
                '</p>'
                ;
                $url = Mage::getUrl('sales/order/'.$urlAppend,array('order_id' => $order->getId()));
                $data = array(
                    'title'         => $title,
                    'link'          => $url,
                    'description'   => $description,
                );
                $rssObj->_addEntry($data);
            }
        }
        $title = Mage::helper('Magento_Rss_Helper_Data')->__('Order #%s created at %s', $order->getIncrementId(), $this->formatDate($order->getCreatedAt()));
        $url = Mage::getUrl('sales/order/view',array('order_id' => $order->getId()));
        $description = '<p>'.
            Mage::helper('Magento_Rss_Helper_Data')->__('Current Status: %s<br/>',$order->getStatusLabel()).
            Mage::helper('Magento_Rss_Helper_Data')->__('Total: %s<br/>',$order->formatPrice($order->getGrandTotal())).
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
