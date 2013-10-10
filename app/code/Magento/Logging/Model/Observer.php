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
     * Instance of \Magento\Logging\Model\Logging
     *
     * @var \Magento\Logging\Model\Processor
     */
    protected $_processor;

    /**
     * Core http
     *
     * @var \Magento\Core\Helper\Http
     */
    protected $_coreHttp = null;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * @var \Magento\Logging\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\User\Model\User
     */
    protected $_user;

    /**
     * @var \Magento\Logging\Model\Event
     */
    protected $_event;

    /**
     * Request
     *
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_request;

    /**
     * Flag model factory
     *
     * @var \Magento\Logging\Model\FlagFactory
     */
    protected $_flagFactory;

    /**
     * @var \Magento\Logging\Model\Resource\EventFactory
     */
    protected $eventFactory;

    /**
     * @param \Magento\Logging\Model\Resource\EventFactory $eventFactory
     * @param \Magento\Logging\Model\Config $config
     * @param \Magento\User\Model\User $user
     * @param \Magento\Logging\Model\Event $event
     * @param \Magento\Core\Helper\Http $coreHttp
     * @param \Magento\Logging\Model\Processor $processor
     * @param \Magento\Core\Model\Config $coreConfig
     * @param \Magento\Core\Controller\Request\Http $request
     * @param \Magento\Logging\Model\FlagFactory $flagFactory
     */
    public function __construct(
        \Magento\Logging\Model\Resource\EventFactory $eventFactory,
        \Magento\Logging\Model\Config $config,
        \Magento\User\Model\User $user,
        \Magento\Logging\Model\Event $event,
        \Magento\Core\Helper\Http $coreHttp,
        \Magento\Logging\Model\Processor $processor,
        \Magento\Core\Model\Config $coreConfig,
        \Magento\Core\Controller\Request\Http $request,
        \Magento\Logging\Model\FlagFactory $flagFactory
    ) {
        $this->eventFactory = $eventFactory;
        $this->_config = $config;
        $this->_user = $user;
        $this->_event = $event;
        $this->_coreHttp = $coreHttp;
        $this->_processor = $processor;
        $this->_coreConfig = $coreConfig;
        $this->_request = $request;
        $this->_flagFactory = $flagFactory;
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
        if (!$this->_config->isEventGroupLogged($eventCode)) {
            return;
        }
        $success = (bool)$userId;
        if (!$userId) {
            $userId = $this->_user->loadByUsername($username)->getId();
        }
        $this->_event->setData(array(
            'ip'         => $this->_coreHttp->getRemoteAddr(),
            'user'       => $username,
            'user_id'    => $userId,
            'is_success' => $success,
            'fullaction' => "{$this->_request->getRouteName()}_{$this->_request->getControllerName()}"
                . "_{$this->_request->getActionName()}",
            'event_code' => $eventCode,
            'action'     => 'login',
        ));
        return $this->_event->save();
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
            $this->eventFactory->create()->rotate(
                3600 * 24 *(int)$this->_coreConfig->getValue('system/rotation/lifetime', 'default')
            );
        }
        $lastRotationFlag->setFlagData(time())->save();
    }
}
