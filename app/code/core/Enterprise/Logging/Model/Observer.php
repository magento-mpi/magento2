<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Logging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Enterprise_Logging Observer class. 
 * It processes all events storing, by handling an actions from core.
 * 
 * Typical procedure is next:
 * 1) Check if event dispatching enabled in system config, by calling model->isActive('event-name')
 * 2) Get data from observer object 
 * 3) Get IP and user_id
 * 4) Get success
 * 5) Set data to event. Note that 'info' must be after event_code, action, success. Those data are neccessary for retrieve info pattern
 *
 */

class Enterprise_Logging_Model_Observer
{
    /**
     * @var Mage_Core_Model_Mysql4_Store_Group_Collection
     */
    protected $_storeGroupCollection;

    /**
     * Store event on product view (catalog_product/edit/id)
     *
     * @param Varien_Object $observer  expected product variable setted
     *
     */
    public function addEventAfterProductView($observer)
    {
        $event = Mage::getModel('logging/event');
        if(!$event->isActive('products'))
            return true;
        $product = $observer->getProduct();
        $id = $product->getId();
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();

        $info = array($product->getSku());
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('products');
        $event->setAction('view');
        $event->setSuccess(true);
        $event->setInfo($info);
        $event->setTime(Mage::app()->getLocale()->date()->toString("YYYY-MM-dd HH:mm:ss"));
        $event->save();
    }

    /**
     * Store event on product save (catalog_product/edit/id)
     *
     * @param Varien_Object $observer  expected product variable setted
     *
     */
    public function addEventOnProductSave($observer)
    {
        $event = Mage::getModel('logging/event');
        if(!$event->isActive('products'))
            return true;

        $product = $observer->getProduct();
        $id = $product->getId();
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();

        $info = array($product->getSku());
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setAction('save');
        $event->setSuccess(true);
        $event->setEventCode('products');
        $event->setInfo($info);
        $event->setTime(Mage::app()->getLocale()->date()->toString("YYYY-MM-dd HH:mm:ss"));
        $event->save();
    }

    /**
     * Store event on product view (catalog_product/edit/id)
     *
     * @param Varien_Object $observer  expected product variable setted
     *
     */
    public function addEventOnProductDelete($observer)
    {
        $event = Mage::getModel('logging/event');
        if(!$event->isActive('products'))
            return true;

        $data = $observer->getInfo();
        $id = $product->getId();
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $info = array($data['sku']);

        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setAction('delete');
        $event->setEventCode('products');
        $event->setSuccess(true);
        $event->setInfo($info);
        $event->setTime(Mage::app()->getLocale()->date()->toString("YYYY-MM-dd HH:mm:ss"));
        $event->save();
    }

    /**
     * Store event after login. Event throwed by Admin/Model/User
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventAfterLogin($observer) 
    {
        $event = Mage::getModel('logging/event');
        if(!$event->isActive('adminlogin'))
            return true;

        $id = $observer->getUserId();
        $action = 'login';
        
        $ip = $_SERVER['REMOTE_ADDR'];
        $username = $observer->getUsername();
        $success = ($id > 0);
        $info = array($username);

        $event = Mage::getModel('logging/event');
        $event->setIp($ip);
        $event->setUserId($id);
        $event->setEventCode('adminlogin');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(Mage::app()->getLocale()->date()->toString("YYYY-MM-dd HH:mm:ss"));
        $event->save();
    }

    /**
     * Store event on category save.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnCategorySave($observer) 
    {
        $event = Mage::getModel('logging/event');
        if(!$event->isActive('categories'))
            return true;
        $action = 'save';

	    
        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();        
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = ($obsrever->getStatus() == 'success' ? 1 : 0);
	$category = $observer->getCategory();
        $info = array($category->getName());

        $event = Mage::getModel('logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('categories');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(Mage::app()->getLocale()->date()->toString("YYYY-MM-dd HH:mm:ss"));
        $event->save();
    }

    public function addEventOnCategoryView($observer) 
    {
        $event = Mage::getModel('logging/event');
        if(!$event->isActive('categories'))
            return true;
        $action = 'view';
        
        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = 1;
	$category = $observer->getCategory();
        $info = array($category->getName());

        $event = Mage::getModel('logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('categories');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(Mage::app()->getLocale()->date()->toString("YYYY-MM-dd HH:mm:ss"));
        $event->save();
    }


}
