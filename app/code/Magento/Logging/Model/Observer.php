<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Log admin actions and performed changes.
 * It doesn't log all admin actions, only listed in logging.xml config files.
 */
namespace Magento\Logging\Model;

class Observer
{

    /**
     * Instance of Magento_Logging_Model_Logging
     *
     * @var \Magento\Logging\Model\Processor
     */
    protected $_processor;

    /**
     * Core http
     *
     * @var Magento_Core_Helper_Http
     */
    protected $_coreHttp = null;

    /**
     * @param Magento_Core_Helper_Http $coreHttp
     * @param Magento_Logging_Model_Processor $processor
     */
    public function __construct(
        Magento_Core_Helper_Http $coreHttp,
        Magento_Logging_Model_Processor $processor
    ) {
        $this->_coreHttp = $coreHttp;
        $this->_processor = $processor;
    }

    /**
     * Mark actions for logging, if required
     *
     * @param \Magento\Event\Observer $observer
     */
    public function controllerPredispatch($observer)
    {
        /* @var $action \Magento\Core\Controller\Varien\Action */
        $action = $observer->getEvent()->getControllerAction();
        /* @var $request \Magento\Core\Controller\Request\Http */
        $request = $observer->getEvent()->getControllerAction()->getRequest();

        $beforeForwardInfo = $request->getBeforeForwardInfo();

        // Always use current action name bc basing on
        // it we make decision about access granted or denied
        $actionName = $request->getRequestedActionName();

        if (empty($beforeForwardInfo)) {
            $fullActionName = $action->getFullActionName();
        } else {
            $fullActionName = array($request->getRequestedRouteName());

            if (isset($beforeForwardInfo['controller_name'])) {
                $fullActionName[] = $beforeForwardInfo['controller_name'];
            } else {
                $fullActionName[] = $request->getRequestedControllerName();
            }

            if (isset($beforeForwardInfo['action_name'])) {
                $fullActionName[] = $beforeForwardInfo['action_name'];
            } else {
                $fullActionName[] = $actionName;
            }

            $fullActionName = implode('_', $fullActionName);
        }

        $this->_processor->initAction($fullActionName, $actionName);
    }

    /**
     * Model after save observer.
     *
     * @param \Magento\Event\Observer
     */
    public function modelSaveAfter($observer)
    {
        $this->_processor->modelActionAfter($observer->getEvent()->getObject(), 'save');
    }

    /**
     * Model after delete observer.
     *
     * @param \Magento\Event\Observer
     */
    public function modelDeleteAfter($observer)
    {
        $this->_processor->modelActionAfter($observer->getEvent()->getObject(), 'delete');
    }

    /**
     * Model after load observer.
     *
     * @param \Magento\Event\Observer
     */
    public function modelLoadAfter($observer)
    {
        $this->_processor->modelActionAfter($observer->getEvent()->getObject(), 'view');
    }

    /**
     * Log marked actions
     *
     * @param \Magento\Event\Observer $observer
     */
    public function controllerPostdispatch($observer)
    {
        if ($observer->getEvent()->getControllerAction()->getRequest()->isDispatched()) {
            $this->_processor->logAction();
        }
    }

    /**
     * Log successful admin sign in
     *
     * @param \Magento\Event\Observer $observer
     */
    public function adminSessionLoginSuccess($observer)
    {
        $this->_logAdminLogin($observer->getUser()->getUsername(), $observer->getUser()->getId());
    }

    /**
     * Log failure of sign in
     *
     * @param \Magento\Event\Observer $observer
     */
    public function adminSessionLoginFailed($observer)
    {
        $eventModel = $this->_logAdminLogin($observer->getUserName());

        if (class_exists('Magento\Pci\Model\Observer', false) && $eventModel) {
            $exception = $observer->getException();
            if ($exception->getCode() == \Magento\Pci\Model\Observer::ADMIN_USER_LOCKED) {
                $eventModel->setInfo(__('User is locked'))->save();
            }
        }
    }

    /**
     * Log sign in attempt
     *
     * @param string $username
     * @param int $userId
     * @return \Magento\Logging\Model\Event
     */
    protected function _logAdminLogin($username, $userId = null)
    {
        $eventCode = 'admin_login';
        if (!\Mage::getSingleton('Magento\Logging\Model\Config')->isEventGroupLogged($eventCode)) {
            return;
        }
        $success = (bool)$userId;
        if (!$userId) {
            $userId = \Mage::getSingleton('Magento\User\Model\User')->loadByUsername($username)->getId();
        }
        $request = \Mage::app()->getRequest();
        /** @var \Magento\Logging\Model\Event $event */
        $event = \Mage::getSingleton('Magento\Logging\Model\Event');
        $event->setData(array(
            'ip'         => $this->_coreHttp->getRemoteAddr(),
            'user'       => $username,
            'user_id'    => $userId,
            'is_success' => $success,
            'fullaction' => "{$request->getRouteName()}_{$request->getControllerName()}_{$request->getActionName()}",
            'event_code' => $eventCode,
            'action'     => 'login',
        ));
        return $event->save();
    }

    /**
     * Cron job for logs rotation
     */
    public function rotateLogs()
    {
        $lastRotationFlag = \Mage::getModel('Magento\Logging\Model\Flag')->loadSelf();
        $lastRotationTime = $lastRotationFlag->getFlagData();
        $rotationFrequency = 3600 * 24 * (int)\Mage::getConfig()->getValue('system/rotation/frequency', 'default');
        if (!$lastRotationTime || ($lastRotationTime < time() - $rotationFrequency)) {
            \Mage::getResourceModel('Magento\Logging\Model\Resource\Event')->rotate(
                3600 * 24 *(int)\Mage::getConfig()->getValue('system/rotation/lifetime', 'default')
            );
        }
        $lastRotationFlag->setFlagData(time())->save();
    }
}
