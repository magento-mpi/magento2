<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Log admin actions and performed changes.
 * It doesn't log all admin actions, only listed in logging.xml config files.
 */
class Magento_Logging_Model_Observer
{
    /**
     * Instance of Magento_Logging_Model_Logging
     *
     * @var Magento_Logging_Model_Processor
     */
    protected $_processor;

    /**
     * Core http
     *
     * @var Magento_Core_Helper_Http
     */
    protected $_coreHttp = null;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Request
     *
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * Flag model factory
     *
     * @var Magento_Logging_Model_FlagFactory
     */
    protected $_flagFactory;

    /**
     * Construct
     * 
     * @param Magento_Core_Helper_Http $coreHttp
     * @param Magento_Logging_Model_Processor $processor
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Logging_Model_FlagFactory $flagFactory
     */
    public function __construct(
        Magento_Core_Helper_Http $coreHttp,
        Magento_Logging_Model_Processor $processor,
        Magento_Core_Model_Config $coreConfig,
        Magento_Core_Controller_Request_Http $request,
        Magento_Logging_Model_FlagFactory $flagFactory
    ) {
        $this->_coreHttp = $coreHttp;
        $this->_processor = $processor;
        $this->_coreConfig = $coreConfig;
        $this->_request = $request;
        $this->_flagFactory = $flagFactory;
    }

    /**
     * Mark actions for logging, if required
     *
     * @param Magento_Event_Observer $observer
     */
    public function controllerPredispatch($observer)
    {
        /* @var $action Magento_Core_Controller_Varien_Action */
        $action = $observer->getEvent()->getControllerAction();
        /* @var $request Magento_Core_Controller_Request_Http */
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
     * @param Magento_Event_Observer
     */
    public function modelSaveAfter($observer)
    {
        $this->_processor->modelActionAfter($observer->getEvent()->getObject(), 'save');
    }

    /**
     * Model after delete observer.
     *
     * @param Magento_Event_Observer
     */
    public function modelDeleteAfter($observer)
    {
        $this->_processor->modelActionAfter($observer->getEvent()->getObject(), 'delete');
    }

    /**
     * Model after load observer.
     *
     * @param Magento_Event_Observer
     */
    public function modelLoadAfter($observer)
    {
        $this->_processor->modelActionAfter($observer->getEvent()->getObject(), 'view');
    }

    /**
     * Log marked actions
     *
     * @param Magento_Event_Observer $observer
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
     * @param Magento_Event_Observer $observer
     */
    public function adminSessionLoginSuccess($observer)
    {
        $this->_logAdminLogin($observer->getUser()->getUsername(), $observer->getUser()->getId());
    }

    /**
     * Log failure of sign in
     *
     * @param Magento_Event_Observer $observer
     */
    public function adminSessionLoginFailed($observer)
    {
        $eventModel = $this->_logAdminLogin($observer->getUserName());

        if (class_exists('Magento_Pci_Model_Observer', false) && $eventModel) {
            $exception = $observer->getException();
            if ($exception->getCode() == Magento_Pci_Model_Observer::ADMIN_USER_LOCKED) {
                $eventModel->setInfo(__('User is locked'))->save();
            }
        }
    }

    /**
     * Log sign in attempt
     *
     * @param string $username
     * @param int $userId
     * @return Magento_Logging_Model_Event
     */
    protected function _logAdminLogin($username, $userId = null)
    {
        $eventCode = 'admin_login';
        if (!Mage::getSingleton('Magento_Logging_Model_Config')->isEventGroupLogged($eventCode)) {
            return;
        }
        $success = (bool)$userId;
        if (!$userId) {
            $userId = Mage::getSingleton('Magento_User_Model_User')->loadByUsername($username)->getId();
        }
        /** @var Magento_Logging_Model_Event $event */
        $event = Mage::getSingleton('Magento_Logging_Model_Event');
        $event->setData(array(
            'ip'         => $this->_coreHttp->getRemoteAddr(),
            'user'       => $username,
            'user_id'    => $userId,
            'is_success' => $success,
            'fullaction' => "{$this->_request->getRouteName()}_{$this->_request->getControllerName()}"
                . "_{$this->_request->getActionName()}",
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
        $lastRotationFlag = $this->_flagFactory->create()->loadSelf();
        $lastRotationTime = $lastRotationFlag->getFlagData();
        $rotationFrequency = 3600 * 24 * (int)$this->_coreConfig->getValue('system/rotation/frequency', 'default');
        if (!$lastRotationTime || ($lastRotationTime < time() - $rotationFrequency)) {
            Mage::getResourceModel('Magento_Logging_Model_Resource_Event')->rotate(
                3600 * 24 *(int)$this->_coreConfig->getValue('system/rotation/lifetime', 'default')
            );
        }
        $lastRotationFlag->setFlagData(time())->save();
    }
}
