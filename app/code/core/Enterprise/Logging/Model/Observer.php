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
     * Pre dispatch observer
     */
    public function catchActionStart($observer)
    {
        $contr = $observer->getControllerAction();
        $action = $contr->getFullActionName();
        if (preg_match("%^adminhtml_(.*?)$%", $action, $m)) {
            $action = $m[1];
        } else {
            return;
        }
        if (!Mage::helper('enterprise_logging')->isActive($action)) {
            return;
        }

        /**
         * Skip view action after save. For example on 'save and continue' click.
         * Some modules always reloading page after save. We pass comma-separated list
         * of actions into getSkipLoggingAction, it is neccesseary for such actions
         * like customer balance, when customer balance ajax tab loaded after
         * customer page.
         */
        if ($act = Mage::getSingleton('admin/session')->getSkipLoggingAction()) {
            if (is_array($act)) {
                $denied = $act;
            } else {
                $denied = explode(',', $act);
            }
            if (in_array($action, $denied)) {
                $deniedThatLeft = array();
                foreach ($denied as $d) {
                    if ($action != $d) {
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
        /**
         * Register action in global.
         */
        Mage::register('enterprise_logged_actions', $action);

        /**
         *  Currently used for only customerbalance actions.
         */
        $this->_checkSpecialActions($action);
    }

    /**
     * Model after save observer. Checks if the model has class defined
     * in logging.xml <model> node.
     *
     * If so, save model in register. It is neccessary to get entity_id
     * on post dispatch for all saving actions including creation.
     *
     * Supports custom observer, from
     * <after_save_handler> node.
     */

    public function catchModelAfterSave($observer)
    {
        if (!Mage::registry('enterprise_logged_actions')) {
            return;
        }

        $model = $observer->getObject();
        $actions = Mage::registry('enterprise_logged_actions');
        if (!is_array($actions)) {
            $actions = array($actions);
        }
        /**
         * List all saved actions to check if we need save current model for some.
         */
        foreach ($actions as $action) {
            $conf = Mage::helper('enterprise_logging')->getConfig($action);
            if (isset($conf['after_save_handler'])) {
                /**
                 * Load custom handler from config
                 */
                $method = $conf['after_save_handler'];
                $object = $this;
                if (preg_match("%^(.*?)::(.*?)$%", $conf['after_save_handler'], $m)) {
                    $method = $m[2];
                    $object = Mage::getModel($m[1]);
                }
                return $object->$method($model, $conf);
            } else {
                /**
                 * Check if model has to be saved. Using deprecated in php5.2 'is_a' which should
                 * be restored in php 5.3. So you may remove '@' if you use php 5.3 or higher.
                 */
                if ($conf && isset($conf['model']) && ($class = Mage::getConfig()->getModelClassName($conf['model'])) && ($model instanceof $class)) {
                    if (isset($conf['allow_model_repeat']) && $conf['allow_model_repeat']) {
                        if (!Mage::registry('saved_model_'.$action)) {
                            Mage::register('saved_model_'.$action, $model);
                        }
                    } else {
                        Mage::register('saved_model_'.$action, $model);
                    }
                }
            }
        }
    }

    /**
     * Orders status history update handler
     */

    public function addCommentSave($model, $conf)
    {
        if ( ($model instanceof Mage_Sales_Model_Order_Status_History) && !Mage::registry('saved_model_sales_order_addComment')) {
            Mage::register('saved_model_sales_order_addComment', $model);
        }
    }

    /**
     * Post-dispatch observer
     */
    public function catchActionEnd($observer)
    {
        if ($actions = Mage::registry('enterprise_logged_actions')) {
            if (!is_array($actions)) {
                $actions = array($actions);
            }
            $ip = $_SERVER['REMOTE_ADDR'];
            $username = null;
            if (Mage::getSingleton('admin/session')->isLoggedIn()) {
                $userId = Mage::getSingleton('admin/session')->getUser()->getId();
                $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
            } else {
                $userId = 0;
            }

            foreach ($actions as $action) {
                $errors  = Mage::getModel('adminhtml/session')->getMessages()->getErrors();
                $isError = (is_array($errors) && count($errors) > 0);
                $info = $this->getInfo($action, $isError);
                if (!$info) {
                    continue;
                }
                $singleton = Mage::getModel('enterprise_logging/event')
                  ->setIp($ip)
                  ->setUser($username)
                  ->setUserId($userId)
                  ->setSuccess(!$isError)
                  ->setFullaction($action)
                  ->setInfo($info)
                  ->setTime(time())
                  ->save();
            }
            Mage::unregister('enterprise_logged_actions');
        }
    }

    /**
     * Check for customer balance actions
     */

    protected function _checkSpecialActions($action)
    {
        if ($action == 'system_store_save') {
            $postData = Mage::app()->getRequest()->getPost();
            switch ($postData['store_type']) {
            case 'website':
                Mage::unregister('enterprise_logged_actions');
                Mage::register('enterprise_logged_actions', 'system_website_save');
                break;
            case 'group':
                Mage::unregister('enterprise_logged_actions');
                Mage::register('enterprise_logged_actions', 'system_storeview_save');
                break;
            }
        } else if ($action == 'sales_order_invoice_save') {
            $request = Mage::app()->getRequest();
            $data = $request->getParam('invoice');
            if (isset($data['do_shipment']) && $data['do_shipment'] == 1) {
                $actions = Mage::registry('enterprise_logged_actions');
                if (!is_array($actions)) {
                    $actions = array($actions);
                }
                $actions[] = 'sales_order_shipment_save';
                Mage::unregister('enterprise_logged_actions');
                Mage::register('enterprise_logged_actions', $actions);
            }
        } else {
            $conf = Mage::helper('enterprise_logging')->getConfig($action);
            if (isset($conf['special_action_handler'])) {
                $object = $this;
                $method = $conf['special_action_handler'];
                if (preg_match("%^(.*?)::(.*?)$%", $conf['special_action_handler'], $m)) {
                    $method = $m[2];
                    $object = Mage::getModel($m[1]);
                }
                $object->$method($action);
            }
        }
    }

    /**
     * Custom handler for category move
     */
    public function categoryMoveHandler($config, $success)
    {
        return array(
            'event_code' => $config['event'],
            'event_action' => $config['action'],
            'event_message' => Mage::app()->getRequest()->getParam('id')
        );
    }

    /**
     * Custom handler for global search
     */
    public function getGlobalSearchAction($config, $success)
    {
        return array(
            'event_code'    => $config['event'],
            'event_action'  => $config['action'],
            'event_message' => Mage::app()->getRequest()->getParam('query')
        );
    }

    /**
     * Custom handler for catalog price rules apply
     */
    public function promocatalogApply($config, $success) {
        $request = Mage::app()->getRequest();
        $message = $request->getParam('rule_id') ? $request->getParam('rule_id') : 'all rules';
        return array(
            'event_code' => $config['event'],
            'event_action' => $config['action'],
            'event_message' => $message
        );
    }

    /**
     * Custom handler for customer validation fail's action
     */
    public function getCustomerValidateAction($config, $success) {
        $out = json_decode(Mage::app()->getResponse()->getBody());
        if ( !empty($out->error)) {
            $id = Mage::app()->getRequest()->getParam('id');
            return array(
                'event_code' => $config['event'],
                'event_action' => $config['action'],
                'event_message' => $id == 0 ? '-' : $id,
                'event_status'  => 0
            );
        }
        return false;
    }


    /**
     * Custom handler for poll save fail's action
     */
    public function getPollValidationAction($config, $success) {
        $out = json_decode(Mage::app()->getResponse()->getBody());
        if ( !empty($out->error)) {
            $id = Mage::app()->getRequest()->getParam('id');
            return array(
                'event_code' => $config['event'],
                'event_action' => $config['action'],
                'event_message' => $id == 0 ? '-' : $id,
                'event_status'  => 0
            );
        } else {
            $poll = Mage::registry('saved_model_poll_validate');
            if ($poll && $poll->getId()) {
                return array(
                    'event_code' => $config['event'],
                    'event_action' => $config['action'],
                    'event_message' => $poll->getId(),
                    'event_status'  => 1
                );
            }
        }
        return false;
    }

    /**
     * Custom switcher for tax_class_save, to distinguish product and customer tax classes
     */
    public function getTaxClassSaveActionInfo($config, $success)
    {
        if (Mage::app()->getRequest()->getParam('class_type') == 'PRODUCT') {
            $config['event'] = 'producttaxclasses';
        }
        return $this->getSaveActionInfo($config, $success);
    }

    /**
     * Custom tax import handler
     */
    public function getTaxRatesImportAction($config, $success)
    {
        if (!Mage::app()->getRequest()->isPost()) {
            return false;
        }
        return array(
            'event_code' => $config['event'],
            'event_action' => $config['action'],
            'event_message' => "tax rates import"
        );
    }

    /**
     * Common view-actions handler
     */

    public function getViewActionInfo($config, $success)
    {
        $code = $config['event'];
        $act = $config['action'];
        $id = isset($config['id'])? $config['id'] : 'id';
        $id = Mage::app()->getRequest()->getParam($id);
        if (!$id && isset($config['default']))
            $id = $config['default'];

        /**
         * Skip if no id
         */
        if ($id === false || $id === null) {
            return false;
        }
        if ($id === 0 && isset($config['skip_zero_id']) && $config['skip_zero_id'])
            return false;

        return array(
            'event_code' => $code,
            'event_action' => $act,
            'event_message' => $id,
        );
    }

    /**
     * Common delete-actions handler
     */

    public function getDeleteActionInfo($config, $success)
    {
        $code = $config['event'];
        $act = $config['action'];
        $id = isset($config['id'])? $config['id'] : 'id';

        $id = Mage::app()->getRequest()->getParam($id);
        return array(
            'event_code' => $code,
            'event_action' => $act,
            'event_message' => $id,
        );
    }

    /**
     * Common save-action handler
     */

    public function getSaveActionInfo($config, $success)
    {
        $code = $config['event'];
        $act = $config['action'];
        $id = isset($config['id'])? $config['id'] : 'id';
        $class = isset($config['model']) ? $config['model'] : '';
        $class = Mage::getConfig()->getModelClassName($class);

        $action = $config['base_action'];


        $request = Mage::app()->getRequest();

        $model = Mage::registry('saved_model_'.$action);

        /**
         * Here is where actual id for logging is taken. If no model given in registry, or model type
         * does not corresponds config value, or 'use_request' node is set in config - take id from
         * request.
         * If no 'use_request' param set that mean that save was not successfull so, fail an event
         *
         */
        if ($model == null || !($model instanceof $class) || (isset($config['use_request']) && $config['use_request'])) {
            $id = Mage::app()->getRequest()->getParam($id);
            /**
             * Fail event, if there is no custom force to request
             */
            if (!isset($config['use_request']) || !$config['use_request'])
                $success = 0;
        } else {
            $id = $model->getId();
        }

        if ( ($id === false) && isset($config['skip_on_empty_id']) && $config['skip_on_empty_id']) {
            return false;
        }

        /**
         * Set actions to skip. We use 'back' and '_continue' params to make redirect after save.
         * We could also force some action skipping in some cases (when no special params in request)
         */

        if ($success && ($request->getParam('back') || $request->getParam('_continue') || isset($config['force_skip']))) {
            /**
             * Comma-separated actions to be skipped next time. If no settings in config, set action by replacing
             * '_save' to '_edit'. Replace occures in only action ending with '_save'.
             */
            $actionToSkip = (isset($config['skip_on_back']) ? $config['skip_on_back'] : preg_replace('%save$%', 'edit', $action));
            Mage::getSingleton('admin/session')->setSkipLoggingAction($actionToSkip);
        }
        return array(
            'event_code' => $code,
            'event_action' => $act,
            'event_message' => $id,
            'event_status' => $success
        );
    }

    /**
     * Common mass-update handler
     */

    public function getMassUpdateActionInfo($config, $success)
    {
        $code = $config['event'];
        $act = $config['action'];
        $id = isset($config['id'])? $config['id'] : 'id';
        $ids = Mage::app()->getRequest()->getParam($id);
        if (is_array($ids))
            $ids = implode(", ", $ids);

        return array(
            'event_code' => $code,
            'event_action' => $act,
            'event_message' => $ids,
        );
    }


    /**
     * Mass-update product attributes
     */

    public function getProductMassAttributeUpdateAction($config, $success)
    {
        $code = $config['event'];
        $act = $config['action'];
        $ids = implode(", ", Mage::helper('adminhtml/catalog_product_edit_action_attribute')->getProductIds());

        return array(
            'event_code' => $code,
            'event_action' => $act,
            'event_message' => $ids,
        );
    }


    /**
     * Handler for reports
     */
    public function getReport($config)
    {
        return array(
            'event_code' => 'reports',
            'event_action' => 'view',
            'event_message' => substr($config['base_action'], 7)
        );
    }

    /**
     * Handler for forgotpassword
     */
    public function getForgotpasswordAction($config, $success)
    {
        if (Mage::app()->getRequest()->isPost()) {
            $type = Mage::getSingleton('adminhtml/session')->getMessages()->getLastAddedMessage()->getType();
            $success = ($type != 'error');
            if ($model = Mage::registry('saved_model_index_forgotpassword')) {
                $id = $model->getId();
            } else {
                $id = Mage::app()->getRequest()->getParam('email');
            }
            return array(
                'event_code' => 'adminlogin',
                'event_action' => 'forgotpassword',
                'event_message' => $id,
                'event_status' => $success
            );
        }
        return false;
    }

    /**
     * Get info manager. It decides what handler (save, view, delete or custom) will process
     * an action on post-dispatch
     */
    public function getInfo($action, $isError)
    {
        $config = Mage::helper('enterprise_logging')->getConfig($action);
        if (isset($config['handler'])) {
            $method = $config['handler'];
            $object = $this;
            if (preg_match("%^(.*?)::(.*?)$%", $config['handler'], $m)) {
                $method = $m[2];
                $object = Mage::getModel($m[1]);
            }
            return $object->$method($config, !$isError);
        }
        if (in_array($config['action'], array('view', 'save', 'delete', 'massUpdate'))) {
            $method = sprintf("get%sactioninfo", $config['action']);
            return $this->$method($config, !$isError);
        }
    }

    /**
     * special handler for myaccount action
     */
    public function viewMyAccount($config, $success)
    {
        $code = $config['event'];
        $act = $config['action'];

        return array(
            'event_code' => $code,
            'event_action' => $act,
            'event_message' => '-',
            'event_status' => $success
        );
    }


    /**
     * special handler for adminlogging action
     */

    public function viewAdminLogs($config, $success)
    {
        $code = $config['event'];
        $act = $config['action'];
        return array(
            'event_code' => $code,
            'event_action' => $act,
            'event_message' => '-',
            'event_status' => $success
        );
    }

    /**
     * special handler for myaccount action
     */

    public function saveMyAccount($config, $success)
    {
        $code = $config['event'];
        $act = $config['action'];
        if ($success) {
            Mage::getSingleton('admin/session')->setSkipLoggingAction('system_account_index');
        }
        return array(
            'event_code' => $code,
            'event_action' => $act,
            'event_message' => '-',
            'event_status' => $success
        );
    }

    /**
     * special handler for newsletterunsubscribe
     */
    public function getNewsletterUnsubscribeAction($config, $success) {
        $code = $config['event'];
        $act = $config['action'];

        $id = Mage::app()->getRequest()->getParam('subscriber');
        if (is_array($id))
            $id = implode(", ", $id);
        return array(
            'event_code' => $code,
            'event_action' => $act,
            'event_message' => $id,
        );
    }

    /**
     * Store event after success login. Event throwed by Admin/Model/Session
     *
     * @param Varien_Object $observer
     *
     */
    public function catchLoginSuccess($observer)
    {
        $node = Mage::getConfig()->getNode('default/admin/enterprise_logging/adminlogin');
        $enabled = ( (string)$node == '1' ? true : false);
        if (!$enabled) {
            return;
        }

        $event = Mage::getSingleton('enterprise_logging/event');
        $event->setIp($_SERVER['REMOTE_ADDR'])
            ->setUserId($observer->getUser()->getId())
            ->setEventCode('adminlogin')
            ->setFullaction('admin_login')
            ->setInfo(
                array(
                    'event_code' => 'adminlogin',
                    'event_message' => $observer->getUser()->getUsername(),
                    'event_action' => 'login',
                    'event_status' => 1
                ))
            ->setTime(time())
            ->save();
    }

    /**
     * Store event after login fail. Event throwed by Admin/Model/Session
     *
     * @param Varien_Object $observer
     *
     */
    public function catchLoginFail($observer)
    {
        $node = Mage::getConfig()->getNode('default/admin/enterprise_logging/adminlogin');
        $enabled = ( (string)$node == '1' ? true : false);
        if (!$enabled) {
            return;
        }
        $userId = 0;
        $username = $observer->getUserName();
        $message = $username;

        $e = $observer->getException();
        if ($e->getCode() == Enterprise_Pci_Model_Observer::ADMIN_USER_LOCKED) {
            $message = "locked";
        }
        $event = Mage::getSingleton('enterprise_logging/event');
        $event->setIp($_SERVER['REMOTE_ADDR'])
            ->setUserId(0)
            ->setUser($username)
            ->setEventCode('adminlogin')
            ->setFullaction('admin_login')
            ->setInfo(
                array(
                    'event_code' => 'adminlogin',
                    'event_message' => $message,
                    'event_action' => 'login',
                    'event_status' => 0
                ))
            ->setTime(time())
            ->save();
    }

    /**
     * Rotate logs cron task
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
