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
        $event->setInfo($info);
        $event->setEvent('products');
        $event->setTime(Mage::app()->getLocale()->date()->toString("YYYY-MM-dd HH:mm:ss"));
        $event->save();
        /*
        Mage::getResourceModel('logging/event')->save($event);
        */
    }

    public function addEventAfterLogin($observer) {
        $id = $observer->getUserId();
        $action = 'login';
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $info = "";
        if($id > 0) {
            $info = sprintf("Successfully logged in, with login=%s, password=%s, uid=%s", $observer->getUsername(), $observer->getPassword(), $id);        
        } else {
            $info = sprintf("Failed to login, with login=%s, password=%s", $observer->getUsername(), $observer->getPassword());
        }

        $event = Mage::getModel('logging/event');
        $event->setIp(ip2long($ip));
        $event->setUserId($id);
        $event->setAction($action);
        $event->setInfo($info);
        $event->setEvent('admin login');
        $event->setTime(Mage::app()->getLocale()->date()->toString("YYYY-MM-dd HH:mm:ss"));
        $event->save();
    }
}
