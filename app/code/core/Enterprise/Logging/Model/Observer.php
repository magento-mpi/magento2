<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @license    http://www.magentocommerce.com/license/enterprise-edition
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
 * 5) Set data to event.
 *
 */
class Enterprise_Logging_Model_Observer
{

    /**
     * Instance of Enterprise_Logging_Model_Logging
     *
     * @var object Enterprise_Logging_Model_Logging
     */
    protected $_processor;

    public function __construct()
    {
        $this->_processor = Mage::getSingleton('enterprise_logging/processor');
    }

    /**
     * Mark actions for logging, if required
     *
     * @param Varien_Event_Observer $observer
     */
    public function controllerPredispatch($observer)
    {
        $fullActionName = $observer->getControllerAction()->getFullActionName();
        $actionName = $observer->getControllerAction()->getRequest()->getRequestedActionName();
        $this->_processor->initAction($fullActionName, $actionName);
    }

    /**
     * Model after save observer.
     *
     * @param Varien_Event_Observer
     */
    public function modelSaveAfter($observer)
    {
        $this->_processor->modelChangeAfter($observer->getEvent()->getObject(), 'save');
    }

    /**
     * Model after delete observer.
     *
     * @param Varien_Event_Observer
     */
    public function modelDeleteAfter($observer)
    {
        $this->_processor->modelChangeAfter($observer->getEvent()->getObject(), 'delete');
    }

    /**
     * Log marked actions
     *
     * @param Varien_Event_Observer $observer
     */
    public function controllerPostdispatch($observer)
    {
        $this->_processor->logAction();
    }

    /**
     * Log successful admin sign in
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminSessionLoginSuccess($observer)
    {
        $this->_logAdminLogin($observer->getUser()->getUsername(), $observer->getUser()->getId());
    }

    /**
     * Log failure of sign in
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminSessionLoginFailed($observer)
    {
        $eventModel = $this->_logAdminLogin($observer->getUserName());

        if (class_exists('Enterprise_Pci_Model_Observer', false) && $eventModel) {
            $exception = $observer->getException();
            if ($exception->getCode() == Enterprise_Pci_Model_Observer::ADMIN_USER_LOCKED) {
                $eventModel->setInfo(Mage::helper('enterprise_logging')->__('User is locked'))->save();
            }
        }
    }

    /**
     * Log sign in attempt
     *
     * @param string $username
     * @param int $userId
     * @return Enterprise_Logging_Model_Event
     */
    protected function _logAdminLogin($username, $userId = null)
    {
        $eventCode = 'admin_login';
        if (!Mage::getSingleton('enterprise_logging/config')->isActive($eventCode, true)) {
            return;
        }
        $success = (bool)$userId;
        if (!$userId) {
            $userId = Mage::getSingleton('admin/user')->loadByUsername($username)->getId();
        }
        $request = Mage::app()->getRequest();
        return Mage::getSingleton('enterprise_logging/event')->setData(array(
            'ip'         => Mage::helper('core/http')->getRemoteAddr(),
            'user'       => $username,
            'user_id'    => $userId,
            'is_success' => $success,
            'fullaction' => "{$request->getRouteName()}_{$request->getControllerName()}_{$request->getActionName()}",
            'event_code' => $eventCode,
            'action'     => 'login',
        ))->save();
    }

    /**
     * Cron job for logs rotation
     */
    public function rotateLogs()
    {
        $lastRotationFlag = Mage::getModel('enterprise_logging/flag')->loadSelf();
        $lastRotationTime = $lastRotationFlag->getFlagData();
        $rotationFrequency = 3600 * 24 * (int)Mage::getConfig()->getNode('default/system/rotation/frequency');
        if (!$lastRotationTime || ($lastRotationTime < time() - $rotationFrequency)) {
            Mage::getResourceModel('enterprise_logging/event')->rotate(
                3600 * 24 *(int)Mage::getConfig()->getNode('default/system/rotation/lifetime')
            );
        }
        $lastRotationFlag->setFlagData(time())->save();
    }
}
