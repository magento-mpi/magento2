<?php
class Enterprise_Logging_Model_Observer
{
    /**
     * @var Mage_Core_Model_Mysql4_Store_Group_Collection
     */
    protected $_storeGroupCollection;

    public function addEventAfterProductView($observer)
    {
        $product = $observer->getProduct();
        $id = $product->getId();
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $action = 'view';

        $event = Mage::getModel('logging/event');
        $info = sprintf("Product: %s, %s, %s viewed", $id, $product->getName(), $product->getSku());

        $event->setIp(ip2long($ip));
        $event->setUserId($user_id);
        $event->setAction($action);
        $event->setInfo($id);
        $event->setEvent('products');
        $event->setTime(Mage::app()->getLocale()->date()->toString("YYYY-MM-dd HH:mm:ss"));
        $event->save();
        /*
        Mage::getResourceModel('logging/event')->save($event);
        */
    }
}
