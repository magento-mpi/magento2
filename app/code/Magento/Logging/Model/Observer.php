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
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
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
     * @var \Magento\Framework\App\RequestInterface
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
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddress;

    /**
     * @param \Magento\Logging\Model\Resource\EventFactory $eventFactory
     * @param \Magento\Logging\Model\Config $config
     * @param \Magento\User\Model\User $user
     * @param \Magento\Logging\Model\Event $event
     * @param \Magento\Logging\Model\Processor $processor
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $coreConfig
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Logging\Model\FlagFactory $flagFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     */
    public function __construct(
        \Magento\Logging\Model\Resource\EventFactory $eventFactory,
        \Magento\Logging\Model\Config $config,
        \Magento\User\Model\User $user,
        \Magento\Logging\Model\Event $event,
        \Magento\Logging\Model\Processor $processor,
        \Magento\Framework\App\Config\ScopeConfigInterface $coreConfig,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Logging\Model\FlagFactory $flagFactory,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
    ) {
        $this->eventFactory = $eventFactory;
        $this->_config = $config;
        $this->_user = $user;
        $this->_event = $event;
        $this->_processor = $processor;
        $this->_coreConfig = $coreConfig;
        $this->_request = $request;
        $this->_flagFactory = $flagFactory;
        $this->_remoteAddress = $remoteAddress;
    }

    /**
     * Model after save observer.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function modelSaveAfter($observer)
    {
        $this->_processor->modelActionAfter($observer->getEvent()->getObject(), 'save');
    }

    /**
     * Model after delete observer.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function modelDeleteAfter($observer)
    {
        $this->_processor->modelActionAfter($observer->getEvent()->getObject(), 'delete');
    }

    /**
     * Model after load observer.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function modelLoadAfter($observer)
    {
        $this->_processor->modelActionAfter($observer->getEvent()->getObject(), 'view');
    }

    /**
     * Log marked actions
     *
     * @param \Magento\Framework\Event\Observer $observer $observer
     * @return void
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
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function adminSessionLoginSuccess($observer)
    {
        $this->_logAdminLogin($observer->getUser()->getUsername(), $observer->getUser()->getId());
    }

    /**
     * Log failure of sign in
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function adminSessionLoginFailed($observer)
    {
        $eventModel = $this->_logAdminLogin($observer->getUserName());

        if (class_exists('Magento\Pci\Model\Backend\Observer', false) && $eventModel) {
            $exception = $observer->getException();
            if ($exception->getCode() == \Magento\Pci\Model\Backend\Observer::ADMIN_USER_LOCKED) {
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
        $this->_event->setData(
            [
                'ip' => $this->_remoteAddress->getRemoteAddress(),
                'user' => $username,
                'user_id' => $userId,
                'is_success' => $success,
                'fullaction' => "{$this->_request->getRouteName()}_{$this->_request->getControllerName()}" .
                "_{$this->_request->getActionName()}",
                'event_code' => $eventCode,
                'action' => 'login',
            ]
        );
        return $this->_event->save();
    }

    /**
     * Cron job for logs rotation
     *
     * @return void
     */
    public function rotateLogs()
    {
        $lastRotationFlag = $this->_flagFactory->create()->loadSelf();
        $lastRotationTime = $lastRotationFlag->getFlagData();
        $rotationFrequency = 3600 * 24 * (int)$this->_coreConfig->getValue('system/rotation/frequency', 'default');
        if (!$lastRotationTime || $lastRotationTime < time() - $rotationFrequency) {
            $this->eventFactory->create()->rotate(
                3600 * 24 * (int)$this->_coreConfig->getValue('system/rotation/lifetime', 'default')
            );
        }
        $lastRotationFlag->setFlagData(time())->save();
    }
}
