<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Model;

class Processor
{
    /**
     * Logging events config
     *
     * @var \Magento\Logging\Model\Config
     */
    protected $_config;

    /**
     * current event config
     *
     * @var array
     */
    protected $_eventConfig;

    /**
     * Instance of controller handler
     *
     * @var \Magento\Logging\Model\Handler\Controllers
     */
    protected $_controllersHandler;

    /**
     * Instance of model controller
     *
     * @var \Magento\Logging\Model\Handler\Models
     */
    protected $_modelsHandler;

    /**
     * Last action name
     *
     * @var string
     */
    protected $_actionName = '';

    /**
     * Last full action name
     *
     * @var string
     */
    protected $_lastAction = '';

    /**
     * Initialization full action name
     *
     * @var string
     */
    protected $_initAction = '';

    /**
     * Flag that signal that we should skip next action
     *
     * @var bool
     */
    protected $_skipNextAction = false;

    /**
     * Temporary storage for model changes before saving to magento_logging_event_changes table.
     *
     * @var array
     */
    protected $_eventChanges = array();

    /**
     * Collection of affected ids
     *
     * @var array
     */
    protected $_collectedIds = array();

    /**
     * Collection of additional data
     *
     * @var array
     */
    protected $_additionalData = array();

    /**
     * Backend auth session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * Object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Logger model
     *
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * Event model factory
     *
     * @var \Magento\Logging\Model\EventFactory
     */
    protected $_eventFactory;

    /**
     * Request
     *
     * @var \Magento\Core\Controller\Request\Http
     */
    protected $_request;

    /**
     * Core http
     *
     * @var \Magento\Core\Helper\Http
     */
    protected $_httpHelper;

    /**
     * Constructor: initialize configuration model, controller and model handler
     *
     * @param \Magento\Logging\Model\Config $config
     * @param \Magento\Logging\Model\Handler\Models $modelsHandler
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Logging\Model\Handler\ControllersFactory $handlerControllersFactory
     * @param \Magento\Logging\Model\EventFactory $eventFactory
     * @param \Magento\Core\Controller\Request\Http $request
     * @param \Magento\Core\Helper\Http $httpHelper
     */
    public function __construct(
        \Magento\Logging\Model\Config $config,
        \Magento\Logging\Model\Handler\Models $modelsHandler,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\Logger $logger,
        \Magento\Logging\Model\Handler\ControllersFactory $handlerControllersFactory,
        \Magento\Logging\Model\EventFactory $eventFactory,
        \Magento\Core\Controller\Request\Http $request,
        \Magento\Core\Helper\Http $httpHelper
    ) {
        $this->_config = $config;
        $this->_modelsHandler = $modelsHandler;
        $this->_controllersHandler = $handlerControllersFactory->create();
        $this->_authSession = $authSession;
        $this->_backendSession = $backendSession;
        $this->_objectManager = $objectManager;
        $this->_logger = $logger;
        $this->_eventFactory = $eventFactory;
        $this->_request = $request;
        $this->_httpHelper = $httpHelper;
    }

    /**
     * preDispatch action handler
     *
     * @param string $fullActionName Full action name like 'adminhtml_catalog_product_edit'
     * @param string $actionName Action name like 'save', 'edit' etc.
     * @return \Magento\Logging\Model\Processor
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function initAction($fullActionName, $actionName)
    {
        $this->_actionName = $actionName;

        if (!$this->_initAction) {
            $this->_initAction = $fullActionName;
        }

        $this->_lastAction = $fullActionName;

        $this->_eventConfig = $this->_config->getEventByFullActionName($fullActionName);
        $this->_skipNextAction = (!$this->_config->isEventGroupLogged($this->_eventConfig['group_name']));
        if ($this->_skipNextAction) {
            return $this;
        }

        /**
         * Skip view action after save. For example on 'save and continue' click.
         * Some modules always reloading page after save. We pass comma-separated list
         * of actions into getSkipLoggingAction, it is necessary for such actions
         * like customer balance, when customer balance ajax tab loaded after
         * customer page.
         */
        $sessionValue = $this->_authSession->getSkipLoggingAction();
        if ($sessionValue) {
            if (is_array($sessionValue)) {
                $key = array_search($fullActionName, $sessionValue);
                if ($key !== false) {
                    unset($sessionValue[$key]);
                    $this->_authSession->setSkipLoggingAction($sessionValue);
                    $this->_skipNextAction = true;
                    return $this;
                }
            }
        }

        if (isset($this->_eventConfig['skip_on_back'])) {
            $addValue = $this->_eventConfig['skip_on_back'];
            if (!is_array($sessionValue) && $sessionValue) {
                $sessionValue = explode(',', $sessionValue);
            } elseif (!$sessionValue) {
                $sessionValue = array();
            }
            $merge = array_merge($addValue, $sessionValue);
            $this->_authSession->setSkipLoggingAction($merge);
        }
        return $this;
    }

    /**
     * Action model processing.
     * Get defference between data & orig_data and store in the internal modelsHandler container.
     *
     * @param object $model
     * @param string $action
     * @return \Magento\Logging\Model\Processor|false
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function modelActionAfter($model, $action)
    {
        if ($this->_skipNextAction) {
            return false;
        }
        /**
         * These models used when we merge action models with action group models
         */
        $groupExpectedModels = null;
        if ($this->_eventConfig) {
            $eventGroupNode = $this->_config->getEventGroupConfig($this->_eventConfig['group_name']);
            if (isset($eventGroupNode['expected_models'])) {
                $groupExpectedModels = $eventGroupNode['expected_models'];
            }
        }

        /**
         * Exact models in exactly action node
         */
        $expectedModels = isset($this->_eventConfig['expected_models'])
            ? $this->_eventConfig['expected_models'] : false;

        if (!$expectedModels || empty($expectedModels)) {
            if (empty($groupExpectedModels)) {
                return false;
            }
            $usedModels = $groupExpectedModels;
        } else {
            if (isset($expectedModels['@'])
                && isset($expectedModels['@']['extends'])
                && $expectedModels['@']['extends'] == 'merge'
            ) {
                $groupExpectedModels = array_replace_recursive($groupExpectedModels, $expectedModels);
                $usedModels = $groupExpectedModels;
            } else {
                $usedModels = $expectedModels;
            }
        }

        $additionalData = $skipData = array();
        /**
         * Log event changes for each model
         */
        foreach ($usedModels as $className => $params) {

            /**
             * Add custom skip fields per expecetd model
             */
            if (isset($params['skip_data'])) {
                $skipData = array_unique($params['skip_data']);
            }

            /**
             * Add custom additional fields per expecetd model
             */
            if (isset($params['additional_data'])) {
                $additionalData = array_unique($params['additional_data']);
            }
            /**
             * Clean up additional data with skip data
             */
            $additionalData = array_diff($additionalData, $skipData);

            if (!($model instanceof $className)) {
                return false;
            }

            $callback = sprintf('model%sAfter', ucfirst($action));
            $this->collectAdditionalData($model, $additionalData);
            $changes = $this->_modelsHandler->$callback($model, $this);

            /* $changes will not be an object in case of view action */
            if (!is_object($changes)) {
                return $this;
            }
            $changes->cleanupData($skipData);
            if ($changes->hasDifference()) {
                $changes->setSourceName($className);
                $changes->setSourceId($model->getId());
                $this->addEventChanges($changes);
            }
        }
        return $this;
    }

    /**
     * Postdispatch action handler
     *
     * @return \Magento\Logging\Model\Processor|false
     */
    public function logAction()
    {
        if (!$this->_initAction) {
            return false;
        }

        if ($this->_actionName == 'denied') {
            $this->logDeniedAction();
            return $this;
        }

        if ($this->_skipNextAction) {
            return false;
        }

        $loggingEvent = $this->_initLoggingEvent();
        $loggingEvent->setAction($this->_eventConfig['action']);
        $loggingEvent->setEventCode($this->_eventConfig['group_name']);

        try {
            if (!$this->_callPostdispatchCallback($loggingEvent)) {
                return false;
            }

            /* Prepare additional info */
            if ($this->getCollectedAdditionalData()) {
                $loggingEvent->setAdditionalInfo($this->getCollectedAdditionalData());
            }
            $loggingEvent->save();
            $this->_saveEventChanges($loggingEvent);

        } catch (\Exception $e) {
            $this->_logger->logException($e);
            return false;
        }
        return $this;
    }

    /**
     * Initialize logging event
     *
     * @return \Magento\Logging\Model\Event
     */
    private function _initLoggingEvent()
    {
        $username = null;
        $userId   = null;
        if ($this->_authSession->isLoggedIn()) {
            $userId = $this->_authSession->getUser()->getId();
            $username = $this->_authSession->getUser()->getUsername();
        }
        $errors = $this->_backendSession->getMessages()->getErrors();
        /** @var \Magento\Logging\Model\Event $loggingEvent */
        $loggingEvent = $this->_eventFactory->create()->setData(array(
            'ip'            => $this->_httpHelper->getRemoteAddr(),
            'x_forwarded_ip'=> $this->_request->getServer('HTTP_X_FORWARDED_FOR'),
            'user'          => $username,
            'user_id'       => $userId,
            'is_success'    => empty($errors),
            'fullaction'    => $this->_initAction,
            'error_message' => implode("\n", array_map(create_function('$a', 'return $a->toString();'), $errors)),
        ));
        return $loggingEvent;
    }

    /**
     * @param \Magento\Logging\Model\Event$loggingEvent
     * @return \Magento\Logging\Model\Processor|false
     */
    private function _callPostdispatchCallback($loggingEvent)
    {
        $handler = $this->_controllersHandler;
        $callback = 'postDispatchGeneric';

        if (isset($this->_eventConfig['post_dispatch'])) {
            $classPath = explode('::', $this->_eventConfig['post_dispatch']);
            if (count($classPath) == 2) {
                $handler = $this->_objectManager->get(str_replace('__', '/', $classPath[0]));
                $callback = $classPath[1];
            } else {
                $callback = $classPath[0];
            }
            if (!$handler || !$callback || !method_exists($handler, $callback)) {
                $this->_logger->logException(
                    new \Magento\Core\Exception(sprintf("Unknown callback function: %s::%s", $handler, $callback)));
            }
        }

        if (!$handler) {
            return false;
        }

        if (!$handler->$callback($this->_eventConfig, $loggingEvent, $this)) {
            return false;
        }
        return $this;
    }

    /**
     * Save event changes
     *
     * @param \Magento\Logging\Model\Event $loggingEvent
     * @return \Magento\Logging\Model\Processor|false
     */
    private function _saveEventChanges($loggingEvent)
    {
        if (!$loggingEvent->getId()) {
            return false;
        }
        foreach ($this->_eventChanges as $changes) {
            if ($changes && ($changes->getOriginalData() || $changes->getResultData())) {
                $changes->setEventId($loggingEvent->getId());
                $changes->save();
            }
        }
        return $this;
    }

    /**
     * Log "denied" action
     *
     * @return \Magento\Logging\Model\Processor|false
     */
    public function logDeniedAction()
    {
        if ($this->_actionName != 'denied') {
            return false;
        }
        if (!$this->_eventConfig || !$this->_config->isEventGroupLogged($this->_eventConfig['group_name'])) {
            return $this;
        }
        $loggingEvent = $this->_initLoggingEvent();
        $loggingEvent->setAction($this->_eventConfig['action']);
        $loggingEvent->setEventCode($this->_eventConfig['group_name']);
        $loggingEvent->setInfo(__('Access denied'));
        $loggingEvent->setIsSuccess(0);
        $loggingEvent->save();
        return $this;
    }

    /**
     * Collect $model id
     *
     * @param object $model
     * @return null
     */
    public function collectId($model)
    {
        $this->_collectedIds[get_class($model)][] = $model->getId();
    }

    /**
     * Collected ids getter
     *
     * @return array
     */
    public function getCollectedIds()
    {
        $ids = array();
        foreach ($this->_collectedIds as $className => $classIds) {
            $uniqueIds  = array_unique($classIds);
            $ids        = array_merge($ids, $uniqueIds);
            $this->_collectedIds[$className] = $uniqueIds;
        }
        return $ids;
    }

    /**
     * Collect $model additional attributes
     *
     * @example
     * Array
     *     (
     *          [Magento_Sales_Model_Order] => Array
     *             (
     *                 [68] => Array
     *                     (
     *                         [increment_id] => 100000108,
     *                         [grand_total] => 422.01
     *                     )
     *                 [94] => Array
     *                     (
     *                         [increment_id] => 100000121,
     *                         [grand_total] => 492.77
     *                     )
     *              )
     *     )
     *
     * @param object $model
     * @param array $attributes
     * @return null
     */
    public function collectAdditionalData($model, array $attributes)
    {
        $attributes = array_unique($attributes);
        if ($modelId = $model->getId()) {
            foreach ($attributes as $attribute) {
                $value = $model->getDataUsingMethod($attribute);
                if (!empty($value)) {
                    $this->_additionalData[get_class($model)][$modelId][$attribute] = $value;
                }
            }
        }
    }

    /**
     * Collected additional attributes getter
     *
     * @return array
     */
    public function getCollectedAdditionalData()
    {
        return $this->_additionalData;
    }

    /**
     * Add new event changes
     *
     * @param \Magento\Logging\Model\Event\Changes $eventChange
     * @return \Magento\Logging\Model\Processor
     */
    public function addEventChanges($eventChange)
    {
        $this->_eventChanges[] = $eventChange;
        return $this;
    }
}
