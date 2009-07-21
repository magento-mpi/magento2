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
class Enterprise_Logging_Model_Processor
{
    /**
     * Logging events config
     *
     * @var object
     */
    protected $_config;

    /**
     * current event config
     *
     * @var object
     */
    protected $_eventConfig;

    /**
     * Instance of controller handler
     *
     * @var oblect
     */
    protected $_controllerActionsHandler;

    /**
     * Instance of model controller
     *
     * @var unknown_type
     */
    protected $_modelsHandler;

    /**
     * Last action name
     *
     * @var string
     */
    protected $_actionName;

    /**
     * Last full action name
     *
     * @var string
     */
    protected $_lastAction;

    /**
     * Initialization full action name
     *
     * @var string
     */
    protected $_initAction;

    /**
     * Flag that signal that we should skip next action
     *
     * @var bool
     */
    protected $_skipNextAction;

    /**
     * Temporary storage for model changes before saving to enterprise_logging_event_changes table.
     *
     * @var array
     */
    protected $_eventChanges = array();

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        $this->_config = Mage::getSingleton('enterprise_logging/config');
        $this->_modelsHandler = Mage::getModel('enterprise_logging/handler_models');
        $this->_controllerActionsHandler = Mage::getModel('enterprise_logging/handler_controllers');
        $this->_lastAction = null;
        $this->_initAction = null;
        $this->_skipAction = false;
    }

    /**
     * preDispatch action handler
     *
     * @param string $fullActionName
     * @param string $actionName
     */
    public function initAction($fullActionName, $actionName)
    {
        $this->setActionName($fullActionName, $actionName);

        $this->_skipNextAction = (!$this->_config->isActive($fullActionName)) ? true : false;
        if ($this->_skipNextAction) {
            return;
        }
        $this->_eventConfig = $this->_config->getNode($fullActionName);

        /**
         * Skip view action after save. For example on 'save and continue' click.
         * Some modules always reloading page after save. We pass comma-separated list
         * of actions into getSkipLoggingAction, it is neccesseary for such actions
         * like customer balance, when customer balance ajax tab loaded after
         * customer page.
         */
        if ($action = Mage::getSingleton('admin/session')->getSkipLoggingAction()) {
            $doNotLog = (is_array($action)) ? $action : explode(',', $action);
            if ($key = array_search($fullActionName, $doNotLog)) {
                unset($doNotLog[$key]);
                $doNotLog = (count($doNotLog)) ? implode(',', $doNotLog) : false;
                Mage::getSingleton('admin/session')->setSkipLoggingAction($doNotLog);
                $this->_skipNextAction = true;
                return;
            }
        }
        if ($this->_eventConfig && isset($this->_eventConfig->skip_on_back)) {
            $addValue = (string)$this->_eventConfig->skip_on_back;
            Mage::getSingleton('admin/session')->setSkipLoggingAction(
                $addValue .','.Mage::getSingleton('admin/session')->getSkipLoggingAction());
        }
    }

    /**
     * Action model processing.
     * Get defference between data & orig_data and store in the internal modelsHandler container.
     *
     * @param object $model
     */
    public function modelChangeAfter($model, $action)
    {
        if ($this->_skipNextAction) {
            return;
        }
        $expectedModels = isset($this->_eventConfig->expected_models)
            ? $this->_eventConfig->expected_models->asArray() : false;
        if (!$expectedModels || empty($expectedModels)) {
            return;
        }

        foreach ($expectedModels as $expect=>$val) {
            $callback = (string)$this->_eventConfig->expected_models->$expect;
            $className = Mage::getConfig()->getModelClassName(str_replace('__', '/', $expect));
            if ($model instanceof $className){
                $callbackFunction = $this->_getModelCallbackFunctionName($action, $callback);
                if ($changes = $this->_modelsHandler->$callbackFunction($model)) {
                    $changes->setModelName($className);
                    $changes->setModelId($model->getId());
                    $this->_eventChanges[] = $changes;
                }
            }
        }
    }

    /**
     * Postdispach action handler
     *
     */
    public function logAction(){
            if (!$this->_initAction) {
                return;
            }

            $username = null;
            $userId   = null;
            if (Mage::getSingleton('admin/session')->isLoggedIn()) {
                $userId = Mage::getSingleton('admin/session')->getUser()->getId();
                $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
            }
            $errors = Mage::getModel('adminhtml/session')->getMessages()->getErrors();
            $loggingEvent = Mage::getModel('enterprise_logging/event')->setData(array(
                'ip'            => Mage::helper('core/http')->getRemoteAddr(),
                'x_forwarded_ip'=> Mage::app()->getRequest()->getServer('HTTP_X_FORWARDED_FOR'),
                'user'          => $username,
                'user_id'       => $userId,
                'is_success'    => !(is_array($errors) && count($errors) > 0),
                'fullaction'    => $this->_initAction,
                'error_message' => implode("\n", array_map(create_function('$a', 'return $a->toString();'), $errors)),
            ));

            if ($this->_actionName == 'denied') {
                $loggingEvent->setInfo('Access Denied');
                $loggingEvent->setIsSuccess(0);
                $loggingEvent->save();
                return;
            }

            if ($this->_skipNextAction) {
                return;
            }

            $loggingEvent->setAction($this->_config->getNode($this->_initAction)->action);
            $loggingEvent->setEventCode($this->_eventConfig->getParent()->getParent()->getName());
            try {
                $callback = (isset($this->_eventConfig->post_dispatch) ? (string)$this->_eventConfig->post_dispatch
                    : ($this->_eventConfig->action == 'view')?'postDispatchGenericView':'postDispatchGeneric');
                if ($this->_controllerActionsHandler->$callback($this->_eventConfig, $loggingEvent, $this)) {
                    $loggingEvent->save();
                    $eventId = $loggingEvent->getId();
                    foreach ($this->_eventChanges as $changes){
                        if($changes) {
                            $changes->setEventId($eventId);
                            $changes->save();
                        }
                    }
                }
            } catch (Mage_Core_Exception $e) {
                Mage::logException($e);
            }
    }

    /**
     * Get name of the action
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->_actionName;
    }


    /**
     * Set action name
     *
     * @param string $actionName
     */
    public function setActionName($fullActionName, $actionName)
    {
        $this->_actionName = $actionName;
        if(!$this->_initAction){
            $this->_initAction = $fullActionName;
        }
        $this->_lastAction = $fullActionName;
    }

    /**
     * Determine callback function of the model handler
     *
     * @param string $action
     * @param string $callback
     * @return string Callback function name
     */
    protected function _getModelCallbackFunctionName($action, $callback)
    {
        if (method_exists($this->_modelsHandler, $callback)) {
            return $callback;
        }
        return sprintf('model%sAfter', ucfirst($action));
    }

    /**
     * Collected ids getter
     *
     * @return array
     */
    public function getCollectedIds()
    {
        return $this->_modelsHandler->getCollectedIds();
    }
}
