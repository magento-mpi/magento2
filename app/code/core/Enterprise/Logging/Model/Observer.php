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
     * Handler model for pre/postdispatches
     *
     * @var Enterprise_Logging_Model_Handler_Controller
     */
    protected $_controllerActionsHandler;

    /**
     * Handler for models afterSave
     *
     * @var Enterprise_Logging_Model_Handler_Models
     */
    protected $_modelsHandler;

    public function __construct()
    {
        $this->_controllerActionsHandler = Mage::getModel('enterprise_logging/handler_controllers');
        $this->_modelsHandler = Mage::getModel('enterprise_logging/handler_models');
    }

    /**
     * Mark actions for logging, if required
     *
     * @param Varien_Event_Observer $observer
     */
    public function controllerPredispatch($observer)
    {
        $fullActionName = $observer->getControllerAction()->getFullActionName();
        if (!Mage::getSingleton('enterprise_logging/config')->isActive($fullActionName)) {
            return;
        }

        /**
         * Skip view action after save. For example on 'save and continue' click.
         * Some modules always reloading page after save. We pass comma-separated list
         * of actions into getSkipLoggingAction, it is neccesseary for such actions
         * like customer balance, when customer balance ajax tab loaded after
         * customer page.
         */
        if ($action = Mage::getSingleton('admin/session')->getSkipLoggingAction()) {
            if (is_array($action)) {
                $denied = $action;
            } else {
                $denied = explode(',', $action);
            }
            if (in_array($fullActionName, $denied)) {
                $deniedThatLeft = array();
                foreach ($denied as $d) {
                    if ($fullActionName != $d) {
                        $deniedThatLeft[] = $d;
                    }
                }
                if (count($deniedThatLeft)) {
                    Mage::getSingleton('admin/session')->setSkipLoggingAction(implode(',', $deniedThatLeft));
                } else {
                    Mage::getSingleton('admin/session')->setSkipLoggingAction(false);
                }
                return;
            }
        }

        // register full action name as to be logged (TODO: implement as ->expect() in controllerActionsHandler
        Mage::register('enterprise_logged_actions', $fullActionName);

        if ($fullActionName == 'adminhtml_system_store_save') { // TODO: move into controllerActionsHandler
            $postData = Mage::app()->getRequest()->getPost();
            switch ($postData['store_type']) { // bug here
            case 'website':
                Mage::unregister('enterprise_logged_actions');
                Mage::register('enterprise_logged_actions', 'adminhtml_system_website_save');
                break;
            case 'group':
                Mage::unregister('enterprise_logged_actions');
                Mage::register('enterprise_logged_actions', 'adminhtml_system_storeview_save');
                break;
            }
        } else if ($fullActionName == 'adminhtml_sales_order_invoice_save') { // TODO: move into controllerActionsHandler
            $request = Mage::app()->getRequest();
            $data = $request->getParam('invoice');
            if (isset($data['do_shipment']) && $data['do_shipment'] == 1) {
                $actions = Mage::registry('enterprise_logged_actions');
                if (!is_array($actions)) {
                    $actions = array($actions);
                }
                $actions[] = 'adminhtml_sales_order_shipment_save';
                Mage::unregister('enterprise_logged_actions');
                Mage::register('enterprise_logged_actions', $actions);
            }
        } else {
            $specialHandler = (string)Mage::getSingleton('enterprise_logging/config')
                ->getNode($fullActionName)->special_action_handler;
            if ($specialHandler) {
                $this->_invokeModel($specialHandler, $fullActionName, $this->_controllerActionsHandler);
            }
        }
    }

    /**
     * Model after save observer. Checks if the model has class defined
     * in logging.xml <model> node.
     *
     * If so, save the model in registry. Later, postDispatch handler will attempt to get saved model entity id
     *
     * Supports custom observer, from <after_save_handler> node.
     *
     * @param Varien_Event_Observer
     */
    public function modelSaveAfter($observer)
    {
        $fullActionNames = Mage::registry('enterprise_logged_actions');
        if (!$fullActionNames) {
            return;
        }
        if (!is_array($fullActionNames)) {
            $fullActionNames = array($fullActionNames);
        }

        $model = $observer->getObject();
        // list all saved actions to check if we need save current model for some.
        foreach ($fullActionNames as $fullActionName) {
            $config = Mage::getSingleton('enterprise_logging/config')->getNode($fullActionName);
            $afterSaveHandler = (string)$config->model_save_after;
            if (!$afterSaveHandler) {
                $afterSaveHandler = 'saveAfterGeneric';
            }
            return $this->_invokeModel($afterSaveHandler, array($model, $config), $this->_modelsHandler);
        }
    }

    /**
     * Log marked actions
     *
     * @param Varien_Event_Observer $observer
     */
    public function controllerPostdispatch($observer)
    {
        if ($fullActionNames = Mage::registry('enterprise_logged_actions')) {
            if (!is_array($fullActionNames)) {
                $fullActionNames = array($fullActionNames);
            }
            $username = null;
            $userId   = 0;
            if (Mage::getSingleton('admin/session')->isLoggedIn()) {
                $userId = Mage::getSingleton('admin/session')->getUser()->getId();
                $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
            }

            foreach ($fullActionNames as $fullActionName) {
                $errors = Mage::getModel('adminhtml/session')->getMessages()->getErrors();
                $config = Mage::getSingleton('enterprise_logging/config')->getNode($fullActionName);

                // prepare event data
                $loggingEvent = Mage::getModel('enterprise_logging/event')->setData(array(
                    'ip'         => $_SERVER['REMOTE_ADDR'],
                    'user'       => $username,
                    'user_id'    => $userId,
                    'is_success' => !(is_array($errors) && count($errors) > 0),
                    'fullaction' => $fullActionName,
                ));

                // attempt to pick a callback for saving model
                $callback = false;
                if (!$config) {
                    // log an error
                    $loggingEvent->addData(array(
                        'event_code' => 'unknown_action',
                        'action'     => 'error',
                    ))->save();
                }
                elseif ($config->post_dispatch) {
                    $callback = (string)$config->post_dispatch;
                }
                elseif (in_array((string)$config->action, array('view', 'save', 'delete', 'massUpdate'))) {
                    $callback = sprintf('postDispatchGeneric%s', ucfirst($config->action));
                }
                if ($callback) {
                    $loggingEvent->addData(array(
                        'event_code' => $config->getParent()->getParent()->getName(),
                        'action'     => (string)$config->action,
                    ));
                    // callback should return non-empty value in order to save model
                    if ($this->_invokeModel($callback, array($config, $loggingEvent), $this->_controllerActionsHandler)) {
                        $loggingEvent->save();
                    }
                }
            }
            Mage::unregister('enterprise_logged_actions');
        }
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
    protected function _logAdminLogin($username, $userId = 0)
    {
        $eventCode = 'admin_login';
        if (!Mage::getSingleton('enterprise_logging/config')->isActive($eventCode, true)) {
            return;
        }
        $request = Mage::app()->getRequest();
        return Mage::getSingleton('enterprise_logging/event')->setData(array(
            'ip'         => $_SERVER['REMOTE_ADDR'],
            'user'       => $username,
            'user_id'    => $userId,
            'is_success' => (bool)$userId,
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

    /**
     * Invoke a model/singleton by specified string
     *
     * @param string $invokeString
     * @param mixed $callbackParams
     * @param object $defaultObject
     * @param bool $isSingleton
     * @return mixed
     */
    protected function _invokeModel($invokeString, $callbackParams = array(), $defaultObject = null, $isSingleton = true)
    {
        if (null === $defaultObject) {
            $defaultObject = $this;
        }
        $object = $defaultObject;
        $method = $invokeString;
        if (preg_match("/^(.*?)::(.*?)$/", $invokeString, $matches)) {
            list(, $factoryModelName, $method) = $matches;
            if ($isSingleton) {
                $object = Mage::getSingleton($factoryModelName);
            } else {
                $object = Mage::getModel($factoryModelName);
            }
        }
        if (!is_array($callbackParams)) {
            $callbackParams = array($callbackParams);
        }
        return call_user_func_array(array($object, $method), $callbackParams);
    }
}
