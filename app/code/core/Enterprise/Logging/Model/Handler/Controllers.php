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
 * Handles generic and specific logic for logging on pre/postdispatch
 *
 * All action handlers may take the $config and $eventModel params, which are configuration node for current action and
 * the event model respectively
 *
 * Action will be logged only if the handler returns non-empty value
 *
 */
class Enterprise_Logging_Model_Handler_Controllers
{

    /**
     * Generic View handler
     *
     * Expects an ID in request
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return bool
     */
    public function postDispatchGenericView($config, $eventModel)
    {
        // lookup entity ID in request
        $id = Mage::app()->getRequest()->getParam($config->id ? (string)$config->id : 'id');
        if (!$id && $config->default) {
            $id = (string)$config->default;
        }
        if (false === $id || null === $id) {
            return false;
        }
        if (0 === $id && (bool)(string)$config->skip_zero_id) {
            return false;
        }
        $eventModel->setInfo($id);
        return true;
    }

    /**
     * Generic Delete handler
     *
     * Expects an ID in request
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchGeneric($config, $eventModel, $processorModel)
    {
        if ($collectedIds = $processorModel->getCollectedIds()) {
            $eventModel->setInfo(implode(', ', $collectedIds));
            return true;
        }
        return false;
    }

    /**
     * Generic Save handler
     *
     * Expects a model in registry, that was saved
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return bool
     */
    public function postDispatchGenericSave($config, $eventModel)
    {
        $class = Mage::getConfig()->getModelClassName($config->expected_model ? (string)$config->expected_model : '');
        $request = Mage::app()->getRequest();

        /**
         * Here is where actual id for logging is taken. If no model given in registry, or model type
         * does not corresponds config value, or 'use_request' node is set in config - take id from
         * request.
         * If no 'use_request' param set that mean that save was not successfull so, fail an event
         *
         */
        if ($model == null || !($model instanceof $class) || ($config->use_request)) {
            $id = $request->getParam($config->id ? (string)$config->id : 'id');
            // mark as failed, if there is no custom force to request
            if (!$config->use_request) {
                $eventModel->setIsSuccess(false);
            }
        }
        // normally get saved model ID
        else {
            $id = $model->getId();
        }

        if (($id === false) && $config->skip_on_empty_id) {
            return false;
        }

        /**
         * Set actions to skip. We use 'back' and '_continue' params to make redirect after save.
         * We could also force some action skipping in some cases (when no special params in request)
         */
        if ($eventModel->getIsSuccess() && ($request->getParam('back') || $request->getParam('_continue') || $config->force_skip)) {
            /**
             * Comma-separated actions to be skipped next time. If no settings in config, set action by replacing
             * '_save' to '_edit'. Replace occures in only action ending with '_save'.
             */
            Mage::getSingleton('admin/session')->setSkipLoggingAction(
                $config->skip_on_back ? (string)$config->skip_on_back : preg_replace('%save$%', 'edit', $config->getName())
            );
        }
        $eventModel->setInfo($id);
        return true;
    }

    /**
     * Generic Mass-update handler
     *
     * Expects ids in request
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return bool
     */
    public function postDispatchGenericMassUpdate($config, $eventModel)
    {
        $ids = Mage::app()->getRequest()->getParam($config->id ? (string)$config->id : 'id');
        if (is_array($ids)) {
            $ids = implode(', ', $ids);
        }
        $eventModel->setInfo($ids);
        return true;
    }

    /**
     * Mass-update product attributes
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchProductMassAttributeUpdate($config, $eventModel)
    {
        return $eventModel->setInfo(
            implode(', ', Mage::helper('adminhtml/catalog_product_edit_action_attribute')->getProductIds())
        );
    }

    /**
     * Custom handler for category move
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchCategoryMove($config, $eventModel)
    {
        return $eventModel->setInfo(Mage::app()->getRequest()->getParam('id'));
    }

    /**
     * Custom handler for global search
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchGlobalSearch($config, $eventModel)
    {
        return $eventModel->setInfo(Mage::app()->getRequest()->getParam('query'));
    }

    /**
     * Handler for forgotpassword
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event|false
     */
    public function postDispatchForgotPassword($config, $eventModel)
    {
        if (Mage::app()->getRequest()->isPost()) {
            if ($model = Mage::registry('enterprise_logging_saved_model_adminhtml_index_forgotpassword')) {
                $info = $model->getId();
            } else {
                $info = Mage::app()->getRequest()->getParam('email');
            }
            return $eventModel->setIsSuccess(
                'error' != Mage::getSingleton('adminhtml/session')->getMessages()->getLastAddedMessage()->getType()
            )->setInfo($info);
        }
        return false;
    }

    /**
     * Custom handler for poll save fail's action
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event|false
     */
    public function postDispatchPollValidation($config, $eventModel)
    {
        $out = json_decode(Mage::app()->getResponse()->getBody());
        if (!empty($out->error)) {
            $id = Mage::app()->getRequest()->getParam('id');
            return $eventModel->setIsSuccess(false)->setInfo($id == 0 ? '' : $id);
        } else {
            $poll = Mage::registry('enterprise_logging_saved_model_adminhtml_poll_validate');
            if ($poll && $poll->getId()) {
                return $eventModel->setIsSuccess(true)->setInfo($poll->getId());
            }
        }
        return false;
    }

    /**
     * Custom handler for customer validation fail's action
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event|false
     */
    public function postDispatchCustomerValidate($config, $eventModel) {
        $out = json_decode(Mage::app()->getResponse()->getBody());
        if (!empty($out->error)) {
            $id = Mage::app()->getRequest()->getParam('id');
            return $eventModel->setIsSuccess(false)->setInfo($id == 0 ? '' : $id);
        }
        return false;
    }

    /**
     * Handler for reports
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event|false
     */
    public function postDispatchReport($config, $eventModel)
    {
        $fullActionNameParts = explode('_report_', $config->getName(), 2);
        if (empty($fullActionNameParts[1])) {
            return false;
        }
        return $eventModel->setInfo($fullActionNameParts[1]);
    }

    /**
     * Custom handler for catalog price rules apply
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchPromoCatalogApply($config, $eventModel)
    {
        $request = Mage::app()->getRequest();
        return $eventModel->setInfo($request->getParam('rule_id') ? $request->getParam('rule_id') : 'all rules');
    }

    /**
     * Special handler for myaccount action
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchMyAccountView($config, $eventModel)
    {
        return $eventModel->setInfo('-');
    }

    /**
     * Special handler for myaccount action
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchMyAccountSave($config, $eventModel)
    {
        if ($eventModel->getIsSuccess()) {
            Mage::getSingleton('admin/session')->setSkipLoggingAction('system_account_index');
        }
        return $eventModel->setInfo('-');
    }

    /**
     * Special handler for newsletter unsubscribe
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchNewsletterUnsubscribe($config, $eventModel)
    {
        $id = Mage::app()->getRequest()->getParam('subscriber');
        if (is_array($id)) {
            $id = implode(', ', $id);
        }
        return $eventModel->setInfo($id);
    }

    /**
     * Custom switcher for tax_class_save, to distinguish product and customer tax classes
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchTaxClassSave($config, $eventModel)
    {
        if (Mage::app()->getRequest()->getParam('class_type') == 'PRODUCT') {
            $eventModel->setEventCode('tax_product_tax_classes');
        }
        return $this->postDispatchGenericSave($config, $eventModel);
    }

    /**
     * Custom tax import handler
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event|false
     */
    public function postDispatchTaxRatesImport($config, $eventModel)
    {
        if (!Mage::app()->getRequest()->isPost()) {
            return false;
        }
        return $eventModel->setInfo(Mage::helper('enterprise_logging')->__('Tax Rates Import'));
    }

    /**
     * Special handler for adminlogging action
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchEnterpriseLoggingReport($config, $eventModel)
    {
        return $eventModel;
    }

    /**
     * Special handler for adminhtml_system_store_save
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchSystemStoreSave($config, $eventModel)
    {
        $postData = Mage::app()->getRequest()->getPost();
        switch ($postData['store_type']) {
        case 'website':
            Mage::unregister('enterprise_logged_actions');
            Mage::register('enterprise_logged_actions', 'adminhtml_system_website_save');
            break;
        case 'group':
            Mage::unregister('enterprise_logged_actions');
            Mage::register('enterprise_logged_actions', 'adminhtml_system_storeview_save');
            break;
        }

    }

    /**
     * Special handler for adminhtml_sales_order_invoice_save
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchSalesOrderInvoiceSave($config, $eventModel)
    {
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
    }
}
