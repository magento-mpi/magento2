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
                $denied_that_left = array();
                foreach ($denied as $d) {
                    if ($action != $d) {
                        $denied_that_left[] = $d;
                    }
                }
                if (count($denied_that_left)) {
                    Mage::getSingleton('admin/session')->setSkipLoggingAction(implode(',', $denied_that_left));
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
     * <after-save-handler> node.
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
            if (isset($conf['after-save-handler'])) {
                /**
                 * Load custom handler from config
                 */
                $method = $conf['after-save-handler'];
                $object = $this;
                if (preg_match("%^(.*?)::(.*?)$%", $conf['after-save-handler'], $m)) {
                    $method = $m[2];
                    $object = Mage::getModel($m[1]);
                }
                return $object->$method($model, $conf);
            } else {
                /**
                 * Check if model has to be saved. Using deprecated in php5.2 'is_a' which should
                 * be restored in php 5.3. So you may remove '@' if you use php 5.3 or higher.
                 */
                if ($conf && isset($conf['model']) && ($class = $conf['model']) && @is_a($model, $class)) {
                    if (isset($conf['allow-multiply-models']) && $conf['allow-multiply-models']) {
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
     * Special after-save handler for invitation.
     * We have a lot of invitations saved (one per each email).
     * This method creates model stub and puts all ids into it
     * separated by ','
     */

    public function invitationAfterSave($model, $conf)
    {
        if ($model instanceof Enterprise_Invitation_Model_Invitation) {
            if ($obj = Mage::registry('saved_model_invitation_save')) {
                $ids = $obj->getId();
                $ids .= ", ".$model->getId();
                /**
                 * Add one more id to list. This trick allows use
                 * standart post-dispatch observer.
                 */
                $obj->setId($ids);
                Mage::unregister('saved_model_invitation_save');
                Mage::register('saved_model_invitation_save', $obj);
            } else {
                /**
                 * Create 'stub' model.
                 */
                $ids = Mage::getModel('enterprise_invitation/invitation');
                $ids->setId($model->getId());
                Mage::register('saved_model_invitation_save', $ids);
            }
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
                $user_id = Mage::getSingleton('admin/session')->getUser()->getId();
                $username = Mage::getSingleton('admin/session')->getUser()->getUsername();
            } else {
                $user_id = 0;
            }

            foreach ($actions as $action) {
                $success = $this->getSuccess($action);
                $info = $this->getInfo($action, $success);
                if (!$info) {
                    continue;
                }
                $singleton = Mage::getModel('enterprise_logging/event')
                  ->setIp($ip)
                  ->setUser($username)
                  ->setUserId($user_id)
                  ->setSuccess($success)
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
        if ($action == 'customer_save') {
            $request = Mage::app()->getRequest();
            $data = $request->getParam('customerbalance');
            if (isset($data['amount_delta']) && $data['amount_delta'] != '') {
                $actions = Mage::registry('enterprise_logged_actions');
                if (!is_array($actions)) {
                    $actions = array($actions);
                }
                $actions[] = 'customerbalance_save';
                Mage::unregister('enterprise_logged_actions');
                Mage::register('enterprise_logged_actions', $actions);
            }
        } else if ($action == 'system_store_save') {

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
            if (isset($conf['special-action-handler'])) {
                $object = $this;
                $method = $config['special-action-handler'];
                if (preg_match("%^(.*?)::(.*?)$%", $config['special-action-handler'], $m)) {
                    $method = $m[2];
                    $object = Mage::getModel($m[1]);
                }
                $object->$method($action);
            }
        }
    }

    /**
     * Custom method for staging mergePost
     */
    public function getMergePostStagingAction($config, $success) {
        $request = Mage::app()->getRequest();
        $staging_id = $request->getParam('id');
        $data = $request->getParam('map');
        $data = $data['websites'];
        $to = 0; 
        foreach ($data['to'] as $element) {
            if ($element) {
                $to = $element;
                break;
            }
        }
        $from = $data['from'][0];
        $info = sprintf("staging_id-%s,from-%s,to-%s", $staging_id, $from, $to);
        if ($schedule = $request->getParam('schedule_merge_later')) {
            $info .= ", scheduled to ".$schedule;
        }
        return array(
            'event_code' => $config['event'],
            'event_action' => $config['action'],
            'event_message' => $info,
        );
    }

    /**
     * Custom method for rollback staging
     */
    public function getRollbackStagingAction($config, $success) {
        $request = Mage::app()->getRequest();
        $backup_id = $request->getParam('backup_id');
        $staging_id = $request->getParam('staging_id');
        $info = sprintf("backup_id-%s, staging_id-%s", $backup_id, $staging_id);

        return array(
            'event_code' => $config['event'],
            'event_action' => $config['action'],
            'event_message' => $info,
        );
    }

    /**
     * Custom method for save staging
     */
    public function getSaveStagingAction($config, $success) {
        $data = Mage::app()->getRequest()->getParam('staging');
        $data = $data['websites'];
        list($master_id, $date) = each($data);

        $class = isset($config['model']) ? $config['model'] : '';
        $action = $config['base_action'];
        $model = Mage::registry('saved_model_'.$action);

        $id = 0;
        if ($model == null || !(@is_a($model, $class))) {
            $success = 0;
        } else {
            $id = $model->getId();
        }
        $info = sprintf("master-%s,staging_id-%s", $master_id, $id);
        return array(
            'event_code' => $config['event'],
            'event_action' => $config['action'],
            'event_message' => $info,
            'event_status' => $success
        );
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
     * Custom handler for pci encryption key change
     */
    public function getPciKeyChangeAction($config, $success)
    {
        return array(
            'event_code' => $config['event'],
            'event_action' => $config['action'],
            'event_message' => Mage::app()->getRequest()->getParam('crypt_key')
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
        return array(
            'event_code' => $config['event'],
            'event_action' => $config['action'],
            'event_message' => 'all rules'
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
        if ($id === 0 && isset($config['skip-zero-id']) && $config['skip-zero-id'])
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

        $action = $config['base_action'];


        $request = Mage::app()->getRequest();

        $model = Mage::registry('saved_model_'.$action);

        /**
         * Here is where actual id for logging is taken. If no model given in registry, or model type
         * does not corresponds config value, or 'use-request' node is set in config - take id from
         * request.
         * If no 'use-request' param set that mean that save was not successfull so, fail an event
         *
         */
        if ($model == null || !(@is_a($model, $class)) || (isset($config['use-request']) && $config['use-request'])) {
            $id = Mage::app()->getRequest()->getParam($id);
            /**
             * Fail event, if there is no custom force to request
             */
            if (!isset($config['use-request']) || !$config['use-request'])
                $success = 0;
        } else {
            $id = $model->getId();
        }

        if ( ($id === false) && isset($config['skip-on-empty-id']) && $config['skip-on-empty-id']) {
            return false;
        }

        /**
         * Set actions to skip. We use 'back' and '_continue' params to make redirect after save.
         * We could also force some action skipping in some cases (when no special params in request)
         */

        if ($success && ($request->getParam('back') || $request->getParam('_continue') || isset($config['force-skip']))) {
            /**
             * Comma-separated actions to be skipped next time. If no settings in config, set action by replacing
             * '_save' to '_edit'. Replace occures in only action ending with '_save'.
             */
            $action_to_skip = (isset($config['skip-on-back']) ? $config['skip-on-back'] : preg_replace('%save$%', 'edit', $action));
            Mage::getSingleton('admin/session')->setSkipLoggingAction($action_to_skip);
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

    public function getInfo($action, $success)
    {
        $config = Mage::helper('enterprise_logging')->getConfig($action);
        if (isset($config['handler'])) {
            $method = $config['handler'];
            $object = $this;
            if (preg_match("%^(.*?)::(.*?)$%", $config['handler'], $m)) {
                $method = $m[2];
                $object = Mage::getModel($m[1]);
            }
            return $object->$method($config, $success);
        }
        if (in_array($config['action'], array('view', 'save', 'delete', 'massUpdate'))) {
            $method = sprintf("get%sactioninfo", $config['action']);
            return $this->$method($config, $success);
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
     * special handler for invitation cancel.
     */
    public function cancelInvitation($config, $success)
    {
        $code = $config['event'];
        $act = $config['action'];
        $id = isset($config['id'])? $config['id'] : 'id';

        $id = Mage::app()->getRequest()->getParam($id);
        Mage::getSingleton('admin/session')->setSkipLoggingAction($config['skip-action']);
        return array(
            'event_code' => $code,
            'event_action' => $act,
            'event_message' => $id,
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
        $node = Mage::getConfig()->getNode('default/admin/logsenabled/adminlogin');
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
        $node = Mage::getConfig()->getNode('default/admin/logsenabled/adminlogin');
        $enabled = ( (string)$node == '1' ? true : false);
        if (!$enabled) {
            return;
        }
        $user_id = 0;
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
     * checks if there were errors in session during request. If so returns fals
     *
     * @param string action
     * @return boolean
     */
    public function getSuccess($action)
    {
        $errors = Mage::getModel('adminhtml/session')->getMessages()->getErrors();
        return !(is_array($errors) && count($errors) > 0);
    }

    /**
     * Rotate logs cron task
     */
    public function rotateLogs()
    {
        $flag = Mage::getModel('enterprise_logging/flag');
        $flag->loadSelf();
        $last_rotate = $flag->getFlagData();
        $eventResource = Mage::getResourceModel('enterprise_logging/event');
        $rotate_frequence = (string)Mage::getConfig()->getNode('default/system/rotation/frequency');
        $interval = (int)$rotate_frequence * 60 * 60 * 24;
        if ($last_rotate > time() - $interval) {
            $eventResource->rotate($interval);
        }
        $flag->setFlagData(time());
        $flag->save();
    }
}