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
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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
     * Generic Action handler
     *
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

    /*
     * Special postDispach handlers below
    */

    /**
     * Simply log action without any id-s
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return bool
     */
    public function postDispatchSimpleSave($config, $eventModel)
    {
        return true;
    }

    /**
     * Custom handler for config view
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchConfigView($config, $eventModel)
    {
        $id = Mage::app()->getRequest()->getParam('section');
        if (!$id) {
            $id = 'general';
        }
        $eventModel->setInfo($id);
        return true;
    }

    /**
     * Custom handler for config save
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @param Enterprise_Logging_Model_Processor $processor
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchConfigSave($config, $eventModel, $processor)
    {
        $request = Mage::app()->getRequest();
        $postData = $request->getPost();
        $groupFieldsData = array();
        $change = Mage::getModel('enterprise_logging/event_changes');

        //Collect skip encrypted fields
        //Look for encrypted node entries in all system.xml files
        $configSections = Mage::getSingleton('adminhtml/config')->getSections();
        $skipEncrypted = array();
        foreach ($configSections->xpath('//sections/*/groups/*/fields/*/backend_model') as $node) {
            if ('adminhtml/system_config_backend_encrypted' === (string)$node) {
                 $skipEncrypted[] = $node->getParent()->getName();
            }
        }
        //For each group of current section creating separated event change
        if (isset($postData['groups'])) {
            foreach ($postData['groups'] as $groupName => $groupData) {
                foreach ($groupData['fields'] as $fieldName => $fieldValueData) {
                    //Clearing config data accordingly to collected skip fields
                    if (!in_array($fieldName, $skipEncrypted) && isset($fieldValueData['value'])) {
                        $groupFieldsData[$fieldName] = $fieldValueData['value'];
                    }
                }

                $processor->addEventChanges(
                    clone $change->setModelName($groupName)
                                 ->setOriginalData(false)
                                 ->setResultData($groupFieldsData)
                );
                $groupFieldsData = array();
            }
        }
        $id = $request->getParam('section');
        if (!$id) {
            $id = 'general';
        }
        return $eventModel->setInfo($id);
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
     * Custom handler for catalog price rules save & apply
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @param Enterprise_Logging_Model_Processor $processorModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchPromoCatalogSaveAndApply($config, $eventModel, $processorModel)
    {
        $request = Mage::app()->getRequest();

        $this->postDispatchGeneric($config, $eventModel, $processorModel);
        if ($request->getParam('auto_apply')) {
            $eventModel->setInfo(Mage::helper('enterprise_logging')->__('%s & applied', $eventModel->getInfo()));
        }

        return $eventModel;
    }

    /**
     * Special handler for my account action
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
     *
     * @deprecated after 1.6.0.0-rc1
     */
    public function postDispatchSystemStoreSave($config, $eventModel){}

    /**
     * Custom handler for catalog product mass attribute update
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchProductUpdateAttributes($config, $eventModel, $processor)
    {
        $request = Mage::app()->getRequest();
        $change = Mage::getModel('enterprise_logging/event_changes');

        $products = Mage::helper('adminhtml/catalog_product_edit_action_attribute')->getProductIds();
        $processor->addEventChanges(clone $change->setModelName('product')
                ->setOriginalData(false)
                ->setResultData(array('ids' => implode(', ', $products))));

        $processor->addEventChanges(clone $change->setModelName('inventory')
                ->setOriginalData(false)
                ->setResultData($request->getParam('inventory', array())));

        $processor->addEventChanges(clone $change->setModelName('attributes')
                ->setOriginalData(false)
                ->setResultData($request->getParam('attributes', array())));

        $websiteIds = $request->getParam('remove_website', array());
        $processor->addEventChanges(clone $change->setModelName('remove_website_ids')
                ->setOriginalData(false)
                ->setResultData(array('ids' => implode(', ', $websiteIds))));

        $websiteIds = $request->getParam('add_website', array());
        $processor->addEventChanges(clone $change->setModelName('add_website_ids')
                ->setOriginalData(false)
                ->setResultData(array('ids' => implode(', ', $websiteIds))));

        return $eventModel->setInfo(Mage::helper('enterprise_logging')->__('Attributes Updated'));
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
        if (!Mage::app()->getRequest()->isPost()) {
            return false;
        }
        return $eventModel->setInfo(Mage::app()->getRequest()->getParam('class_type') . ': ' . Mage::app()->getRequest()->getParam('class_id'));
    }
}
