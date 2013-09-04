<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Logging_Model_Processor
{
    /**
     * Logging events config
     *
     * @var Magento_Logging_Model_Config
     */
    protected $_config;

    /**
     * current handle config
     *
     * @var array
     */
    protected $_handleConfig;

    /**
     * current event config
     *
     * @var array
     */
    protected $_eventConfig;

    /**
     * Instance of controller handler
     *
     * @var Magento_Logging_Model_Handler_Controllers
     */
    protected $_controllersHandler;

    /**
     * Instance of model controller
     *
     * @var Magento_Logging_Model_Handler_Models
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
    protected $_collectedAdditionalData = array();

    /** @var  Magento_Backend_Model_Auth_Session */
    protected $_authSession;

    /** @var  Magento_Backend_Model_Session */
    protected $_backendSession;

    /** @var  Magento_ObjectManager */
    protected $_objectManager;

    /** @var  Magento_Core_Model_App */
    protected $_coreApp;

    /** @var  Magento_Core_Helper_Http */
    protected $_httpHelper;

    /** @var  Magento_Core_Model_Logger */
    protected $_logger;

    /**
     * Constructor: initialize configuration model, controller and model handler
     *
     * @param Magento_Logging_Model_Config $config
     * @param Magento_Logging_Model_Handler_Models $modelsHandler
     * @param Magento_Logging_Model_Handler_Controllers $controllersHandler
     * @param Magento_Backend_Model_Auth_Session $authSession
     * @param Magento_Backend_Model_Session $backendSession
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_App $coreApp
     * @param Magento_Core_Helper_Http $httpHelper
     * @param Magento_Core_Model_Logger $logger
     */
    public function __construct(
        Magento_Logging_Model_Config $config,
        Magento_Logging_Model_Handler_Models $modelsHandler,
        Magento_Logging_Model_Handler_Controllers $controllersHandler,
        Magento_Backend_Model_Auth_Session $authSession,
        Magento_Backend_Model_Session $backendSession,
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_App $coreApp,
        Magento_Core_Helper_Http $httpHelper,
        Magento_Core_Model_Logger $logger
    ) {
        $this->_config = $config;;
        $this->_modelsHandler = $modelsHandler;
        $this->_controllersHandler = $controllersHandler;
        $this->_authSession = $authSession;
        $this->_backendSession = $backendSession;
        $this->_objectManager = $objectManager;
        $this->_coreApp = $coreApp;
        $this->_httpHelper = $httpHelper;
        $this->_logger = $logger;

    }

    /**
     * preDispatch action handler
     *
     * @param string $fullActionName Full action name like 'adminhtml_catalog_product_edit'
     * @param string $actionName Action name like 'save', 'edit' etc.
     */
    public function initAction($fullActionName, $actionName)
    {
        $this->_actionName = $actionName;

        if (!$this->_initAction) {
            $this->_initAction = $fullActionName;
        }

        $this->_lastAction = $fullActionName;

        $this->_skipNextAction = (!$this->_config->isActive($fullActionName)) ? true : false;
        if ($this->_skipNextAction) {
            return;
        }
        $this->_handleConfig = $this->_config->getNode($fullActionName);
        $this->_eventConfig = $this->_config->getEventByFullActionName($fullActionName);

        /**
         * Skip view action after save. For example on 'save and continue' click.
         * Some modules always reloading page after save. We pass comma-separated list
         * of actions into getSkipLoggingAction, it is necessary for such actions
         * like customer balance, when customer balance ajax tab loaded after
         * customer page.
         */
        $doNotLog = $this->_authSession->getSkipLoggingAction();
        if ($doNotLog) {
            if (is_array($doNotLog)) {
                $key = array_search($fullActionName, $doNotLog);
                if ($key !== false) {
                    unset($doNotLog[$key]);
                    $this->_authSession->setSkipLoggingAction($doNotLog);
                    $this->_skipNextAction = true;
                    return;
                }
            }
        }

        if (isset($this->_handleConfig['skip_on_back'])) {
            $addValue = $this->_handleConfig['skip_on_back'];
            $sessionValue = $this->_authSession->getSkipLoggingAction();
            if (!is_array($sessionValue) && $sessionValue) {
                $sessionValue = explode(',', $sessionValue);
            } elseif (!$sessionValue) {
                $sessionValue = array();
            }
            $merge = array_merge($addValue, $sessionValue);
            $this->_authSession->setSkipLoggingAction($merge);
        }
    }

    /**
     * Action model processing.
     * Get defference between data & orig_data and store in the internal modelsHandler container.
     *
     * @param object $model
     * @param string $action
     */
    public function modelActionAfter($model, $action)
    {
        if ($this->_skipNextAction) {
            return;
        }
        /**
         * These models used when we merge action models with action group models
         */
        $defaultExpectedModels = null;
        if ($this->_handleConfig) {
            if (isset($this->_eventConfig['expected_models'])) {
                $defaultExpectedModels = $this->_eventConfig['expected_models'];
            }
        }

        /**
         * Exact models in exactly action node
         */
        $expectedModels = isset($this->_handleConfig['expected_models'])
            ? $this->_handleConfig['expected_models'] : false;

        if (!$expectedModels || empty($expectedModels)) {
            if (empty($defaultExpectedModels)) {
                return;
            }
            $usedModels = $defaultExpectedModels;
        } else {
            if ($expectedModels['@']['extends'] == 'merge') {
                $defaultExpectedModels = array_replace_recursive($defaultExpectedModels, $expectedModels);
                $usedModels = $defaultExpectedModels;
            } else {
                $usedModels = $expectedModels;
            }
        }

        $additionalData = $skipData = array();
        /**
         * Log event changes for each model
         */
        foreach ($usedModels as $className => $callback) {

            /**
             * Add custom skip fields per expecetd model
             */
            if (isset($callback['skip_data'])) {
                $rawData = $callback['skip_data'];
                $skipData = array_unique($rawData);
            }

            /**
             * Add custom additional fields per expecetd model
             */
            if (isset($callback['additional_data'])) {
                $rawData = $callback['additional_data'];
                $additionalData = array_unique($rawData);
            }
            /**
             * Clean up additional data with skip data
             */
            $additionalData = array_diff($additionalData, $skipData);

            if ($model instanceof $className) {
                $classMap = $this->_getCallbackFunction(trim($callback), $this->_modelsHandler,
                    sprintf('model%sAfter', ucfirst($action)));
                $handler  = $classMap['handler'];
                $callback = $classMap['callback'];
                if ($handler) {
                    $this->collectAdditionalData($model, $additionalData);
                    $changes = $handler->$callback($model, $this);
                    /**
                     * Because of logging view action, $changes must be checked if it is an object
                     */
                    if (is_object($changes)) {
                        $changes->cleanupData($skipData);
                        if ($changes->hasDifference()) {
                            $changes->setSourceName($className);
                            $changes->setSourceId($model->getId());
                            $this->addEventChanges($changes);
                        }
                    }
                }
            }
            $skipData = array();
        }
    }

    /**
     * Postdispatch action handler
     */
    public function logAction()
    {
        if (!$this->_initAction) {
            return;
        }
        $username = null;
        $userId   = null;
        if ($this->_authSession->isLoggedIn()) {
            $userId = $this->_authSession->getUser()->getId();
            $username = $this->_authSession->getUser()->getUsername();
        }
        $errors = $this->_backendSession->getMessages()->getErrors();
        /** @var Magento_Logging_Model_Event $loggingEvent */
        $loggingEvent = $this->_objectManager->create('Magento_Logging_Model_Event')
            ->setData(array(
                'ip'            => $this->_httpHelper->getRemoteAddr(),
                'x_forwarded_ip'=> $this->_coreApp->getRequest()->getServer('HTTP_X_FORWARDED_FOR'),
                'user'          => $username,
                'user_id'       => $userId,
                'is_success'    => empty($errors),
                'fullaction'    => $this->_initAction,
                'error_message' => implode("\n", array_map(create_function('$a', 'return $a->toString();'), $errors)),
                )
            );

        if ($this->_actionName == 'denied') {
            $config = $this->_config->getNode($this->_initAction);
            if (!$config || !$this->_config->isActive($this->_initAction)) {
                return;
            }
            $loggingEvent->setAction($config->action);
            $loggingEvent->setEventCode($config->getParent()->getParent()->getName());
            $loggingEvent->setInfo(__('Access denied'));
            $loggingEvent->setIsSuccess(0);
            $loggingEvent->save();
            return;
        }

        if ($this->_skipNextAction) {
            return;
        }

        $loggingEvent->setAction($this->_handleConfig['action']);
        $loggingEvent->setEventCode($this->_eventConfig['name']);

        try {
            $callback = isset($this->_handleConfig['post_dispatch']) ? (string)$this->_handleConfig['post_dispatch'] : false;
            $defaulfCallback = 'postDispatchGeneric';
            $classMap = $this->_getCallbackFunction($callback, $this->_controllersHandler, $defaulfCallback);
            $handler  = $classMap['handler'];
            $callback = $classMap['callback'];
            if (!$handler) {
                return;
            }
            if ($handler->$callback($this->_handleConfig, $loggingEvent, $this)) {
                /**
                 * Prepare additional info
                 */
                if ($this->getCollectedAdditionalData()) {
                    $loggingEvent->setAdditionalInfo($this->getCollectedAdditionalData());
                }
                $loggingEvent->save();
                $eventId = $loggingEvent->getId();
                if ($eventId) {
                    foreach ($this->_eventChanges as $changes) {
                        if ($changes && ($changes->getOriginalData() || $changes->getResultData())) {
                            $changes->setEventId($eventId);
                            $changes->save();
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $this->_logger->logException($e);
        }
    }

    /**
     * Collect $model id
     *
     * @param object $model
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
     */
    public function collectAdditionalData($model, array $attributes)
    {
        $attributes = array_unique($attributes);
        if ($model->getId()) {
            foreach ($attributes as $attribute) {
                $value = $model->getDataUsingMethod($attribute);
                if (!empty($value)) {
                    $this->_collectedAdditionalData[get_class($model)][$model->getId()][$attribute] = $value;
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
        return $this->_collectedAdditionalData;
    }

    /**
     * Get callback function for logAction and modelActionAfter functions
     *
     * @param string $srtCallback
     * @param object $defaultHandler
     * @param string $defaultFunction
     * @return array Contains two values 'handler' and 'callback' that indicate what callback function should be applied
     */
    protected function _getCallbackFunction($srtCallback, $defaultHandler, $defaultFunction)
    {
        $return = array('handler' => $defaultHandler, 'callback' => $defaultFunction);
        if (empty($srtCallback)) {
            return $return;
        }

        try {
            $classPath = explode('::', $srtCallback);
            if (count($classPath) == 2) {
                $return['handler'] = $this->_objectManager->get(str_replace('__', '/', $classPath[0]));
                $return['callback'] = $classPath[1];
            } else {
                $return['callback'] = $classPath[0];
            }
            if (!$return['handler'] || !$return['callback'] || !method_exists($return['handler'],
                $return['callback'])) {
                new Magento_Core_Exception("Unknown callback function: {$srtCallback}");
            }
        } catch (Exception $e) {
            $return['handler'] = false;
            $this->_logger->logException($e);
        }

        return $return;
    }

    /**
     * Add new event changes
     *
     * @param Magento_Logging_Model_Event_Changes $eventChange
     * @return Magento_Logging_Model_Processor
     */
    public function addEventChanges($eventChange)
    {
        $this->_eventChanges[] = $eventChange;
        return $this;
    }
}
