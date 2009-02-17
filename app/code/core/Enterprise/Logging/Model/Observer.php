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
        $code = 10;
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $action = 'view';

        $event = Mage::getModel('logging/event');
        $event->setIp(ip2long($ip));
        $event->setUserId($user_id);
        $event->setAction($action);
        $event->setEntityId($id);
        $event->setEventCode($code);
        $event->setTime(date("Y-m-d H:i:s"));
        $event->save();
        /*
        Mage::getResourceModel('logging/event')->save($event);
        */
    }
}
