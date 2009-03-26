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
    private static $_action;

    /**
     * @var Mage_Core_Model_Mysql4_Store_Group_Collection
     */
    protected $_storeGroupCollection;

    public function catchActionStart($observer) {
        $contr = $observer->getControllerAction();
        $action = $contr->getFullActionName();
        if(preg_match("%^adminhtml_(.*?)$%", $action, $m)) {
            $action = $m[1];
        } else {
            return;
        }
        if(!in_array($action, $this->_getActionsToLog())) {
            return;
        }
        if($act = Mage::getSingleton('admin/session')->getSkipLoggingAction()) {
            if(is_array($act))
                $denied = $act;
            else
                $denied = explode(',', $act);
            if(in_array($action, $denied)) {
                $d2 = array();
                foreach($denied as $d) {
                    if($action != $d)
                        $d2[] = $d;
                }
                if(count($d2))
                    Mage::getSingleton('admin/session')->setSkipLoggingAction(implode(',', $d2));                    
                else
                    Mage::getSingleton('admin/session')->setSkipLoggingAction(false);
                return;
            }
        }
        Mage::register('enterprise_logged_actions', $action);
        $this->_checkSpecialActions($action);
    }

    public function catchModelAfterSave($observer) {
        if(!Mage::registry('enterprise_logged_actions'))
            return;

        $savedModels = Mage::registry('saved_models');
        if(!$savedModels) {
            $savedModels = array();
        }
        $savedModels[] = $observer->getObject();
        Mage::unregister('saved_models');
        Mage::register('saved_models', $savedModels);
    }

    public function catchActionEnd() {
        if($actions = Mage::registry('enterprise_logged_actions')) {
            if(!is_array($actions)) 
                $actions = array($actions);
            $ip = $_SERVER['REMOTE_ADDR'];
            $user_id = Mage::getSingleton('admin/session')->getUser()->getId();

            foreach($actions as $action) {
                $success = $this->getSuccess($action);
                $info = $this->getInfo($action, $success);
                if(!$info)
                    continue;
                Mage::getModel('enterprise_logging/event')
                  ->setIp($ip)
                  ->setUserId($user_id)
                  ->setSuccess($success)
                  ->setFullaction($action)
                  ->setInfo($info)
                  ->setTime(time())
                  ->save();
            }
        }
    }

    protected function _checkSpecialActions($action) {
        if($action == 'customer_save') {
            $request = Mage::app()->getRequest();
            $data = $request->getParam('customerbalance');
            if(isset($data['delta']) && $data['delta'] != '') {
                $actions = Mage::registry('enterprise_logged_actions');
                if(!is_array($actions))
                    $actions = array($actions);
                $actions[] = 'customerbalance_save';
                Mage::unregister('enterprise_logged_actions');
                Mage::register('enterprise_logged_actions', $actions);
            }
        }
    }

    protected function _getActionsToLog() {
        if(!($actions = Mage::registry('enterprise_logged_actions'))) {
            $actions = array(
                'catalog_product_edit',
                'catalog_product_save',
                'catalog_product_delete',
                'catalog_category_edit',
                'catalog_category_save',
                'catalog_category_move',
                'catalog_category_delete',
                'customer_edit',
                'customer_save',
                'customer_delete',
                'customerbalance_form',
                'customerbalance_save'
            );
        }
        return $actions;
    }

    public function getSuccess($action) {
        return true;
        $messages = Mage::getModel('admin/session')->getErrors(true)->getMessages();
        $model = Mage::getModel('enterprise_logging/event');
        foreach($messages as $mes) {
            if($mes->getName() == $model->getErrorName($action))
                return false;
        }
        return true;
    }

    public function getInfo($action, $success) {
        $request = Mage::app()->getRequest();
        switch($action) {
        case 'catalog_product_edit':
            $id = $request->getParam('id');
            if(!$id)
                return false;
            return array(
                'event_code' => 'products',
                'event_action' => 'view',
                'event_message' => $id,
            );
            break;
        case 'catalog_product_save':
            $models = Mage::registry('saved_models');
            $model = null;
            foreach($models as $m) {
                if($m instanceof Mage_Catalog_Model_Product)
                    $model = $m;
            }
            if($model == null) {
                Mage::throwException('Admin Logging error: Unable to log save action');
            }
            $id = $model->getId();
            if ($success && $request->getParam('back')) {
                Mage::getSingleton('admin/session')->setSkipLoggingAction('catalog_product_edit');
            }
            return array(
                'event_code' => 'products',
                'event_action' => 'save',
                'event_message' => $id
            );
            break;
        case 'catalog_product_delete':
            $id = $request->getParam('id');
            return array(
                'event_code' => 'products',
                'event_action' => 'delete',
                'event_message' => $id
            );
            break;
        case 'customer_edit':
            $id = $request->getParam('id');
            if(!$id)
                return false;

            return array(
                'event_code' => 'customers',
                'event_action' => 'view',
                'event_message' => $id
            );
            break;
        case 'customer_save':
            $models = Mage::registry('saved_models');
            $model = null;
            foreach($models as $m) {
                if($m instanceof Mage_Customer_Model_Customer)
                    $model = $m;
            }
            if($model == null) {
                Mage::throwException('Admin Logging error: Unable to log save action (customer)');
            }
            $id = $model->getId();
            if ($success && $request->getParam('back')) {
                Mage::getSingleton('admin/session')->setSkipLoggingAction('customer_edit');
            }
            if ($success && $request->getParam('back')) {
                $den = explode(',', Mage::getSingleton('admin/session')->getSkipLoggingAction());
                $den[] = 'customer_edit';
                Mage::getSingleton('admin/session')->setSkipLoggingAction(implode(',', $den));
            }

            return array(
                'event_code' => 'customers',
                'event_action' => 'save',
                'event_message' => $id,
            );
            break;
        case 'customerbalance_save':
            $models = Mage::registry('saved_models');
            $model = null;
            foreach($models as $m) {
                if($m instanceof Mage_Customer_Model_Customer)
                    $model = $m;
            }
            if($model == null) {
                Mage::throwException('Admin Logging error: Unable to log save action (customerbalance)');
            }
            $id = $model->getId();
            if ($success && $request->getParam('back')) {
                Mage::getSingleton('admin/session')->setSkipLoggingAction('customer_edit');
            }

            if ($success && $request->getParam('back')) {
                $den = explode(',', Mage::getSingleton('admin/session')->getSkipLoggingAction());
                $den[] = 'customerbalance_form';
                Mage::getSingleton('admin/session')->setSkipLoggingAction(implode(',', $den));
            }

            return array(
                'event_code' => 'customerbalance',
                'event_action' => 'save',
                'event_message' => $id
            );
            break;
        case 'customer_delete':
            $id = $request->getParam('id');
            return array(
                'event_code' => 'customers',
                'event_action' => 'delete',
                'event_message' => $id
            );
            break;
        case 'customerbalance_form':
            $id = $request->getParam('id');
            return array(
                'event_code' => 'customerbalance',
                'event_action' => 'view',
                'event_message' => $id
            );
            break;
        }

    }

  
    /**
     * Store event on product view (catalog_product/edit/id)
     *
     * @param Varien_Object $observer  expected product variable setted
     *
     */
    public function addEventAfterProductView($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('products'))
            return true;
        if(Mage::getSingleton('admin/session')->getSkipProductView()) {
            Mage::getSingleton('admin/session')->setSkipProductView(0);
            return true;
        }

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
        $event->setTime(time());
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
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('products'))
            return true;

        $product = $observer->getProduct();
        $id = $product->getId();

        $ip = $_SERVER['REMOTE_ADDR'];
        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $status = $observer->getStatus() == 'success' ? 1 : 0;
        if($status) {
            $skipView = $observer->getRequest()->getParam('back');
            if($skipView) {
                Mage::getSingleton('admin/session')->setSkipProductView(1);
            }
        }

        $info = array($product->getSku());
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setAction('save');
        $event->setSuccess($status);
        $event->setEventCode('products');
        $event->setInfo($info);
        $event->setTime(time());
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
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('products'))
            return true;

        $sku = $observer->getSku();

        $ip = $_SERVER['REMOTE_ADDR'];
        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $info = array($sku);
        $success = $observer->getStatus() == 'success' ? 1 : 0;

        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setAction('delete');
        $event->setEventCode('products');
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event after login. Event throwed by Admin/Model/User
     *
     * @param Varien_Object $observer
     *
     */
    public function addEventAfterLogin($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('adminlogin')) {
            return;
        }

        $event->setIp($_SERVER['REMOTE_ADDR'])
            ->setUserId($observer->getUser()->getId())
            ->setEventCode('adminlogin')->setAction('login')
            ->setSuccess((bool)(int)$observer->getUser()->getId())
            ->setInfo(array($observer->getUsername()))
            ->setTime(time())
            ->save();
    }

    /**
     * Store event on forgotpassword.
     *
     * @param Varien_Object $observer  expected email
     *
     */
    public function addEventOnForgotpassword($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('adminlogin'))
            return true;

        $action = 'forgotpassword';
        $ip = $_SERVER['REMOTE_ADDR'];
        $email = $observer->getEmail();
        $success = 1;
        $info = array($email);

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId(0);
        $event->setEventCode('adminlogin');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
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
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('categories'))
            return true;
        $action = 'save';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = ($observer->getStatus() == 'success' ? 1 : 0);
        if($success) {
            Mage::getSingleton('admin/session')->setSkipCategoryView(1);
        }

        $category = $observer->getCategory();
        $info = array($category->getName());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('categories');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on category view.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnCategoryView($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('categories'))
            return true;
        if(Mage::getSingleton('admin/session')->getSkipCategoryView()) {
            Mage::getSingleton('admin/session')->setSkipCategoryView(0);
            return true;
        }

        $action = 'view';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = 1;
        $category = $observer->getCategory();
        if(!$category->getName())
            return;
        $info = array($category->getName());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('categories');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on category move.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnCategoryMove($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('categories'))
            return true;
        $action = 'move';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = 1;
        $catId = $observer->getCategoryId();
        $prevParent = $observer->getPrevParentId();
        $newParent = $observer->getParentId();
        $cat = Mage::getModel('catalog/category')->load($catId);
        $prev = Mage::getModel('catalog/category')->load($prevParent);
        $new = Mage::getModel('catalog/category')->load($newParent);
        $info = array($cat->getName(), $prev->getName(), $new->getName());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('categories');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time()); //time());
        $event->save();
    }


    /**
     * Store event on category delete.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnCategoryDelete($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('categories'))
            return true;
        $action = 'delete';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        $cat = $observer->getCategory();
        $info = array($cat->getName());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('categories');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }


    /**
     * Store event on cms page view.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnCmspageView($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('cmspages'))
            return true;

        /** Skip on 'save and continue edit' case */
        if(Mage::getSingleton('admin/session')->getSkipCmsPageView()) {
            Mage::getSingleton('admin/session')->setSkipCmsPageView(0);
            return true;
        }

        $action = 'view';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = 1;
        $page = $observer->getCmspage();
        if(!$page->getTitle())
            return;
        $info = array($page->getTitle());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('cmspages');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on cms page save.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnCmspageSave($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('cmspages'))
            return true;
        $action = 'save';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        if($success) {
            $skipView = $observer->getRequest()->getParam('back');
            if($skipView) {
                Mage::getSingleton('admin/session')->setSkipCmsPageView(1);
            }
        }

        $page = $observer->getCmspage();
        $info = array($page->getTitle());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('cmspages');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on cms page delete.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnCmspageDelete($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('cmspages'))
            return true;
        $action = 'delete';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        $title = $observer->getTitle();
        $info = array($title);

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('cmspages');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on cms block view.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnCmsblockView($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('cmsblocks'))
            return true;

        /** Skip on 'save and continue edit' case */
        if(Mage::getSingleton('admin/session')->getSkipCmsBlockView()) {
            Mage::getSingleton('admin/session')->setSkipCmsBlockView(0);
            return true;
        }

        $action = 'view';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = 1;
        $page = $observer->getCmsblock();
        if(!$page->getTitle())
            return;
        $info = array($page->getTitle());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('cmsblocks');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on cms block save.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnCmsblockSave($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('cmsblocks'))
            return true;
        $action = 'save';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        if($success) {
            $skipView = $observer->getRequest()->getParam('back');
            if($skipView) {
                Mage::getSingleton('admin/session')->setSkipCmsBlockView(1);
            }
        }

        $page = $observer->getCmsblock();
        $info = array($page->getTitle());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('cmsblocks');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on cms block delete.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnCmsblockDelete($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('cmsblocks'))
            return true;
        $action = 'delete';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        $title = $observer->getTitle();
        $info = array($title);

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('cmsblocks');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }


    /**
     * Store event on customer view.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnCustomerView($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('customers'))
            return true;
        $action = 'view';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = 1;
        $customer = $observer->getCustomer();
        if(!$customer->getId())
            return;
        $info = array($customer->getId(), $customer->getFirstname());
        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('customers');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on customer save.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnCustomerSave($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('customers'))
            return true;
        $action = 'save';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        $customer = $observer->getCustomer();
        $info = array($customer->getId(), $customer->getFirstname());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('customers');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on customer delete.
     *
     * @param Varien_Object $observer  expected customer
     *
     */
    public function addEventOnCustomerDelete($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('customers'))
            return true;
        $action = 'delete';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        $customer = $observer->getCustomer();
        $info = array($customer->getId(), $customer->getFirstname());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('customers');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on report view.
     *
     * @param Varien_Object $observer  expected report name
     *
     */
    public function addEventOnReportView($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('reports'))
            return true;
        $action = 'view';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = 1;
        $report = $observer->getReport();
        $info = array($report);
        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('reports');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on system config view.
     *
     * @param Varien_Object $observer  expected report name
     *
     */
    public function addEventOnSystemConfigView($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('systemconfiguration'))
            return true;
        $action = 'view';

        /** Skip on 'save and continue edit' case */
        if(Mage::getSingleton('admin/session')->getSkipSystemConfigView()) {
            Mage::getSingleton('admin/session')->setSkipSystemConfigView(0);
            return true;
        }

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = 1;
        $section = $observer->getSection();
        $section = 'general';

        $info = array($section);
        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('systemconfiguration');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on system config save.
     *
     * @param Varien_Object $observer  expected report name
     *
     */
    public function addEventOnSystemConfigSave($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('systemconfiguration'))
            return true;
        $action = 'save';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = 1;
        $section = $observer->getSection();
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        if($success) {
            Mage::getSingleton('admin/session')->setSkipSystemConfigView(1);
        }

        $info = array($section);
        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('systemconfiguration');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on giftaccount add
     *
     * @param Varien_Object $observer  expected code
     *
     */
    public function addEventOnGiftaccountSave($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('giftaccount'))
            return true;
        $action = 'save';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $model = $observer->getModel();
        $model->load($model->getId());
        $code = $model->getCode();
        $success = $observer->getStatus() == 'success' ? 1 : 0;

        $info = array($code);
        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('giftaccount');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on giftaccount view
     *
     * @param Varien_Object $observer  expected code
     *
     */
    public function addEventOnGiftaccountView($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('giftaccount'))
            return true;
        $action = 'view';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $code = $observer->getCode();
        $success = 1;

        $info = array($code);
        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('giftaccount');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }


    /**
     * Store event on giftaccount delete
     *
     * @param Varien_Object $observer  expected code
     *
     */
    public function addEventOnGiftaccountDelete($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('giftaccount'))
            return true;
        $action = 'delete';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $code = $observer->getCode();
        $success = $observer->getStatus() == 'success' ? 1 : 0;

        $info = array($code);
        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('giftaccount');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on customergroup view.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnCustomergroupView($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('customergroups'))
            return true;
        $action = 'view';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = 1;
        $customergroup = $observer->getGroup();
        if(!$customergroup->getId())
            return;
        $info = array($customergroup->getId(), $customergroup->getCustomerGroupCode());
        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('customergroups');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on customergroup save.
     *
     * @param Varien_Object $observer
     *
     */
    public function addEventOnCustomergroupSave($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('customergroups'))
            return true;
        $action = 'save';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        $customergroup = $observer->getGroup();
        $info = array($customergroup->getId(), $customergroup->getCustomerGroupCode());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('customergroups');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on customergroup delete.
     *
     * @param Varien_Object $observer  expected customergroup
     *
     */
    public function addEventOnCustomergroupDelete($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('customergroups'))
            return true;
        $action = 'delete';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        $customergroup = $observer->getGroup();
        $info = array($customergroup->getId(), $customergroup->getCustomerGroupCode());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('customergroups');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on promocatalog view.
     *
     * @param Varien_Object $observer
     *
     */
    public function addEventOnPromocatalogView($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('promocatalog'))
            return true;
        $action = 'view';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = 1;
        $promocatalog = $observer->getPromocatalog();
        if(!$promocatalog->getId())
            return;
        $info = array($promocatalog->getId(), $promocatalog->getName());
        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('promocatalog');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on promocatalog save.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnPromocatalogSave($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('promocatalog'))
            return true;
        $action = 'save';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        $promocatalog = $observer->getPromocatalog();
        $info = array($promocatalog->getId(), $promocatalog->getName());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('promocatalog');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on promocatalog delete.
     *
     * @param Varien_Object $observer  expected promocatalog
     *
     */
    public function addEventOnPromocatalogDelete($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('promocatalog'))
            return true;
        $action = 'delete';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        $promocatalog = $observer->getPromocatalog();
        $info = array($promocatalog->getId(), $promocatalog->getName());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('promocatalog');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on promoquote view.
     *
     * @param Varien_Object $observer
     *
     */
    public function addEventOnPromoquoteView($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('promoquote'))
            return true;
        $action = 'view';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = 1;
        $promoquote = $observer->getPromoquote();
        if(!$promoquote->getId())
            return;
        $info = array($promoquote->getId(), $promoquote->getName());
        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('promoquote');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on promoquote save.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnPromoquoteSave($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('promoquote'))
            return true;
        $action = 'save';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        $promoquote = $observer->getPromoquote();
        $info = array($promoquote->getId(), $promoquote->getName());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('promoquote');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on promoquote delete.
     *
     * @param Varien_Object $observer  expected promoquote
     *
     */
    public function addEventOnPromoquoteDelete($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('promoquote'))
            return true;
        $action = 'delete';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        $promoquote = $observer->getPromoquote();
        $info = array($promoquote->getId(), $promoquote->getName());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('promoquote');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on systemaccount view.
     *
     * @param Varien_Object $observer
     *
     */
    public function addEventOnSystemaccountView($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('systemaccount'))
            return true;
        $action = 'view';

        if(Mage::getSingleton('admin/session')->getSkipSystemaccountView()) {
            Mage::getSingleton('admin/session')->setSkipSystemaccountView(0);
            return;
        }
        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = 1;
        $info = array($username);
        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('systemaccount');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on systemaccount save.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnSystemaccountSave($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('systemaccount'))
            return true;
        $action = 'save';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        $systemaccount = $observer->getSystemaccount();
        $info = array($username);

        if($success) {
            Mage::getSingleton('admin/session')->setSkipSystemaccountView(1);
        }

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('systemaccount');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on catalogevent view.
     *
     * @param Varien_Object $observer  expected username
     *
     */
    public function addEventOnCatalogeventView($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('catalogevents'))
            return true;
        $action = 'view';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = 1;
        $catalogevent = $observer->getCatalogevent();
        if(!$catalogevent->getId())
            return;
        $info = array($catalogevent->getId());
        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('catalogevents');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on catalogevent save.
     *
     * @param Varien_Object $observer
     *
     */
    public function addEventOnCatalogeventSave($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('catalogevents'))
            return true;
        $action = 'save';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        $catalogevent = $observer->getCatalogevent();
        $info = array($catalogevent->getId());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('catalogevents');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on catalogevent delete.
     *
     * @param Varien_Object $observer  expected catalogevent
     *
     */
    public function addEventOnCatalogeventDelete($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('catalogevents'))
            return true;
        $action = 'delete';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        $catalogevent = $observer->getCatalogevent();
        $info = array($catalogevent->getId());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('catalogevents');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }


    /**
     * Store event on Invitation view.
     *
     * @param Varien_Object $observer
     *
     */
    public function addEventOnInvitationView($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('invitations'))
            return true;
        $action = 'view';

        if(Mage::getSingleton('admin/session')->getSkipInvitationView()) {
            Mage::getSingleton('admin/session')->setSkipInvitationView(1);
            return;
        }
        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = 1;
        $invitation = $observer->getInvitation();
        if(!$invitation->getId())
            return;
        $info = array($invitation->getId());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('invitations');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on invitation save.
     *
     * @param Varien_Object $observer
     *
     */
    public function addEventOnInvitationSave($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('invitations'))
            return true;
        $action = 'save';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        if($success) {
            Mage::getSingleton('admin/session')->setSkipInvitationView(1);
        }
        $cnt = $observer->getCnt();
        $inv = $observer->getInvitation();
        if($cnt)
            $mes = "count emails=$cnt";
        else
            $mes = "id=".$inv->getId().", message=".$inv->getMessage();
        $info = array($mes);

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('invitations');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }

    /**
     * Store event on invitation delete.
     *
     * @param Varien_Object $observer  expected invitation
     *
     */
    public function addEventOnInvitationDelete($observer)
    {
        $event = Mage::getModel('enterprise_logging/event');
        if(!$event->isActive('invitations'))
            return true;
        $action = 'delete';

        $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
        $ip = $_SERVER['REMOTE_ADDR'];
        $success = $observer->getStatus() == 'success' ? 1 : 0;
        $invitation = $observer->getInvitation();
        $info = array($invitation->getId());

        $event = Mage::getModel('enterprise_logging/event');
        $event->setIp($ip);
        $event->setUserId($user_id);
        $event->setEventCode('invitations');
        $event->setAction($action);
        $event->setSuccess($success);
        $event->setInfo($info);
        $event->setTime(time());
        $event->save();
    }


    /**
     * Rotate logs cron task
     */
    public function rotateLogs()
    {
        $flag = Mage::getModel('enterprise_logging/flag');
        $flag->loadSelf();
        $last_rotate = $flag->getFlagData();
        $eventResource = Mage::getResourceModel('enterprise_logging/event');
        $rotate_frequence = (string)Mage::getConfig()->getNode('default/system/rotation/frequency');
        $interval = (int)$rotate_frequence * 60 * 60 * 24;
        if(true || $last_rotate > time() - $interval) {
            $eventResource->rotate($interval);
        }
        $flag->setFlagData(time());
        $flag->save();
    }

}
