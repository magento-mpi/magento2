<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Handles generic and specific logic for logging on pre/postdispatch
 *
 * All action handlers may take the $config and $eventModel params, which are configuration node for current action and
 * the event model respectively
 * Action will be logged only if the handler returns non-empty value
 */
class Enterprise_Logging_Model_Handler_Controllers
{
    /**
     * Generic Action handler
     *
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @param Enterprise_Logging_Model_Processor $processorModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchGeneric($config, $eventModel, $processorModel)
    {
        $collectedIds = $processorModel->getCollectedIds();
        if ($collectedIds) {
            $eventModel->setInfo($this->_objectManager->get('Enterprise_Logging_Helper_Data')->implodeValues($collectedIds));
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
     * @param Magento_Simplexml_Element $config
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
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchConfigView($config, $eventModel)
    {
        $sectionId = Mage::app()->getRequest()->getParam('section');
        if (!$sectionId) {
            $sectionId = 'general';
        }
        $eventModel->setInfo($sectionId);
        return true;
    }

    /**
     * Custom handler for config save
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @param Enterprise_Logging_Model_Processor $processor
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchConfigSave($config, $eventModel, $processor)
    {
        $request = Mage::app()->getRequest();
        $postData = $request->getPost();
        $groupFieldsData = array();
        $change = Mage::getModel('Enterprise_Logging_Model_Event_Changes');

        //Collect skip encrypted fields
        /** @var Magento_Backend_Model_Config_Structure $configStructure  */
        $configStructure = Mage::getSingleton('Magento_Backend_Model_Config_Structure');

        $encryptedNodePaths = $configStructure->getFieldPathsByAttribute(
            'backend_model',
            'Magento_Backend_Model_Config_Backend_Encrypted'
        );

        $skipEncrypted = array();
        foreach ($encryptedNodePaths as $path) {
            $skipEncrypted[] = basename($path);
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
                    clone $change->setSourceName($groupName)
                                 ->setOriginalData(array())
                                 ->setResultData($groupFieldsData)
                );
                $groupFieldsData = array();
            }
        }
        $sectionId = $request->getParam('section');
        if (!$sectionId) {
            $sectionId = 'general';
        }
        return $eventModel->setInfo($sectionId);
    }

    /**
     * Custom handler for category move
     *
     * @param Magento_Simplexml_Element $config
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
     * @param Magento_Simplexml_Element $config
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
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event|bool
     */
    public function postDispatchForgotPassword($config, $eventModel)
    {
        if (Mage::app()->getRequest()->isPost()) {
            if ($model = Mage::registry('enterprise_logging_saved_model_adminhtml_index_forgotpassword')) {
                $info = $model->getId();
            } else {
                $info = Mage::app()->getRequest()->getParam('email');
            }
            $success = true;
            $messages = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getMessages()->getLastAddedMessage();
            if ($messages) {
                $success = 'error' != $messages->getType();
            }
            return $eventModel->setIsSuccess($success)->setInfo($info);
        }
        return false;
    }

    /**
     * Custom handler for poll save fail's action
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event|bool
     */
    public function postDispatchPollValidation($config, $eventModel)
    {
        $out = json_decode(Mage::app()->getResponse()->getBody());
        if (!empty($out->error)) {
            $pollId = Mage::app()->getRequest()->getParam('id');
            return $eventModel->setIsSuccess(false)->setInfo($pollId == 0 ? '' : $pollId);
        } else {
            $poll = Mage::registry('current_poll_model');
            if ($poll && $poll->getId()) {
                return $eventModel->setIsSuccess(true)->setInfo($poll->getId());
            }
        }
        return false;
    }

    /**
     * Custom handler for customer validation fail's action
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event|bool
     */
    public function postDispatchCustomerValidate($config, $eventModel)
    {
        $out = json_decode(Mage::app()->getResponse()->getBody());
        if (!empty($out->error)) {
            $customerId = Mage::app()->getRequest()->getParam('id');
            return $eventModel->setIsSuccess(false)->setInfo($customerId == 0 ? '' : $customerId);
        }
        return false;
    }

    /**
     * Handler for reports
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @param Enterprise_Logging_Model_Processor $processor
     * @return Enterprise_Logging_Model_Event|bool
     */
    public function postDispatchReport($config, $eventModel, $processor)
    {
        $fullActionNameParts = explode('_report_', $config->getName(), 2);
        if (empty($fullActionNameParts[1])) {
            return false;
        }

        $request = Mage::app()->getRequest();
        $filter = $request->getParam('filter');

        //Filtering request data
        $data = array_intersect_key($request->getParams(), array(
            'report_from' => null,
            'report_to' => null,
            'report_period' => null,
            'store' => null,
            'website' => null,
            'group' => null
        ));

        //Need when in request data there are was no period info
        if ($filter) {
            $filterData = Mage::app()->getHelper('Magento_Adminhtml_Helper_Data')->prepareFilterString($filter);
            $data = array_merge($data, (array)$filterData);
        }

        //Add log entry details
        if ($data) {
            $change = Mage::getModel('Enterprise_Logging_Model_Event_Changes');
            $processor->addEventChanges($change->setSourceName('params')
                ->setOriginalData(array())
                ->setResultData($data));
        }

        return $eventModel->setInfo($fullActionNameParts[1]);
    }

    /**
     * Custom handler for catalog price rules apply
     *
     * @param Magento_Simplexml_Element $config
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
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @param Enterprise_Logging_Model_Processor $processorModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchPromoCatalogSaveAndApply($config, $eventModel, $processorModel)
    {
        $request = Mage::app()->getRequest();

        $this->postDispatchGeneric($config, $eventModel, $processorModel);
        if ($request->getParam('auto_apply')) {
            $eventModel->setInfo(__('%1 & applied', $eventModel->getInfo()));
        }

        return $eventModel;
    }

    /**
     * Special handler for newsletter unsubscribe
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchNewsletterUnsubscribe($config, $eventModel)
    {
        $subscriberId = Mage::app()->getRequest()->getParam('subscriber');
        if (is_array($subscriberId)) {
            $subscriberId = implode(', ', $subscriberId);
        }
        return $eventModel->setInfo($subscriberId);
    }

    /**
     * Custom tax import handler
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event|bool
     */
    public function postDispatchTaxRatesImport($config, $eventModel)
    {
        if (!Mage::app()->getRequest()->isPost()) {
            return false;
        }
        $success = true;
        $messages = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getMessages()->getLastAddedMessage();
        if ($messages) {
            $success = 'error' != $messages->getType();
        }
        return $eventModel->setIsSuccess($success)->setInfo(__('Tax Rates Import'));
    }

    /**
     * Custom handler for catalog product mass attribute update
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @param Enterprise_Logging_Model_Processor $processor
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchProductUpdateAttributes($config, $eventModel, $processor)
    {
        $request = Mage::app()->getRequest();
        $change = Mage::getModel('Enterprise_Logging_Model_Event_Changes');
        $products = $request->getParam('product');
        if (!$products) {
            $products = $this->_objectManager->get('Magento_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute')->getProductIds();
        }
        if ($products) {
            $processor->addEventChanges(clone $change->setSourceName('product')
                ->setOriginalData(array())
                ->setResultData(array('ids' => implode(', ', $products))));
        }

        $processor->addEventChanges(clone $change->setSourceName('inventory')
                ->setOriginalData(array())
                ->setResultData($request->getParam('inventory', array())));
        $attributes = $request->getParam('attributes', array());
        $status = $request->getParam('status', null);
        if (!$attributes && $status) {
            $attributes['status'] = $status;
        }
        $processor->addEventChanges(clone $change->setSourceName('attributes')
                ->setOriginalData(array())
                ->setResultData($attributes));

        $websiteIds = $request->getParam('remove_website', array());
        if ($websiteIds) {
            $processor->addEventChanges(clone $change->setSourceName('remove_website_ids')
                ->setOriginalData(array())
                ->setResultData(array('ids' => implode(', ', $websiteIds))));
        }

        $websiteIds = $request->getParam('add_website', array());
        if ($websiteIds) {
            $processor->addEventChanges(clone $change->setSourceName('add_website_ids')
                ->setOriginalData(array())
                ->setResultData(array('ids' => implode(', ', $websiteIds))));
        }

        return $eventModel->setInfo(__('Attributes Updated'));
    }

    /**
     * Custom switcher for tax_class_save, to distinguish product and customer tax classes
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchTaxClassSave($config, $eventModel)
    {
        if (!Mage::app()->getRequest()->isPost()) {
            return false;
        }
        $classType = Mage::app()->getRequest()->getParam('class_type');
        $classId = (int)Mage::app()->getRequest()->getParam('class_id');

        return $this->_logTaxClassEvent($classType, $eventModel, $classId);
    }

    /**
     * Custom switcher for tax_class_save, to distinguish product and customer tax classes
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchTaxClassDelete($config, $eventModel)
    {
        if (!Mage::app()->getRequest()->isPost()) {
            return false;
        }
        $classId = (int)Mage::app()->getRequest()->getParam('class_id');
        $classModel = Mage::registry('tax_class_model');
        $classType = $classModel != null ? $classModel->getClassType() : '';

        return $this->_logTaxClassEvent($classType, $eventModel, $classId);
    }

    /**
     * Custom handler for creating System Backup
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchSystemBackupsCreate($config, $eventModel)
    {
        $backup = Mage::registry('backup_manager');

        if ($backup) {
            $eventModel->setIsSuccess($backup->getIsSuccess())
                ->setInfo($backup->getBackupFilename());

            $errorMessage = $backup->getErrorMessage();
            if (!empty($errorMessage)) {
                $eventModel->setErrorMessage($errorMessage);
            }
        } else {
            $eventModel->setIsSuccess(false);
        }
        return $eventModel;
    }

    /**
     * Custom handler for deleting System Backup
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchSystemBackupsDelete($config, $eventModel)
    {
        $backup = Mage::registry('backup_manager');

        if ($backup) {
            $eventModel->setIsSuccess($backup->getIsSuccess())
                ->setInfo($this->_objectManager->get('Enterprise_Logging_Helper_Data')->implodeValues($backup->getDeleteResult()));
        } else {
            $eventModel->setIsSuccess(false);
        }
        return $eventModel;
    }

    /**
     * Custom handler for creating System Rollback
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchSystemRollback($config, $eventModel)
    {
        $backup = Mage::registry('backup_manager');

        if ($backup) {
            $eventModel->setIsSuccess($backup->getIsSuccess())
                ->setInfo($backup->getBackupFilename());

            $errorMessage = $backup->getErrorMessage();
            if (!empty($errorMessage)) {
                $eventModel->setErrorMessage($errorMessage);
            }
        } else {
            $eventModel->setIsSuccess(false);
        }

        return $eventModel;
    }

    /**
     * Custom handler for mass unlocking locked admin users
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchAdminAccountsMassUnlock($config, $eventModel)
    {
        if (!Mage::app()->getRequest()->isPost()) {
            return false;
        }
        $userIds = Mage::app()->getRequest()->getPost('unlock', array());
        if (!is_array($userIds)) {
            $userIds = array();
        }
        if (!$userIds) {
            return false;
        }
        return $eventModel->setInfo(implode(', ', $userIds));
    }

    /**
     * Custom handler for mass reindex process on index management
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchReindexProcess($config, $eventModel)
    {
        $processIds = Mage::app()->getRequest()->getParam('process', null);
        if (!$processIds) {
            return false;
        }
        return $eventModel->setInfo(is_array($processIds) ? implode(', ', $processIds) : (int)$processIds);
    }

    /**
     * Custom handler for System Currency save
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @param Enterprise_Logging_Model_Processor $processor
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchSystemCurrencySave($config, $eventModel, $processor)
    {
        $request = Mage::app()->getRequest();
        $change = Mage::getModel('Enterprise_Logging_Model_Event_Changes');
        $data = $request->getParam('rate');
        $values = array();
        if (!is_array($data)) {
            return false;
        }
        foreach ($data as $currencyCode => $rate) {
            foreach ($rate as $currencyTo => $value) {
                $value = abs($value);
                if ($value == 0) {
                    continue;
                }
                $values[] = $currencyCode . '=>' . $currencyTo . ': ' . $value;
            }
        }

        $processor->addEventChanges($change->setSourceName('rates')
            ->setOriginalData(array())
            ->setResultData(array('rates' => implode(', ', $values))));
        $success = true;
        $messages = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getMessages()->getLastAddedMessage();
        if ($messages) {
            $success = 'error' != $messages->getType();
        }
        return $eventModel->setIsSuccess($success)->setInfo(__('Currency Rates Saved'));
    }

    /**
     * Custom handler for Cache Settings Save
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @param Enterprise_Logging_Model_Processor $processor
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchSaveCacheSettings($config, $eventModel, $processor)
    {
        $request = Mage::app()->getRequest();
        if (!$request->isPost()) {
            return false;
        }
        $info = '-';
        $cacheTypes = $request->getPost('types');
        if (is_array($cacheTypes) && !empty($cacheTypes)) {
            $cacheTypes = implode(', ', $cacheTypes);
            $info = __('Cache types: %1 ', $cacheTypes);
        }

        $success = true;
        $messages = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getMessages()->getLastAddedMessage();
        if ($messages) {
            $success = 'error' != $messages->getType();
        }
        return $eventModel->setIsSuccess($success)->setInfo($info);
    }

    /**
     * Custom tax export handler
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event|bool
     */
    public function postDispatchTaxRatesExport($config, $eventModel)
    {
        if (!Mage::app()->getRequest()->isPost()) {
            return false;
        }
        $success = true;
        $messages = Mage::getSingleton('Magento_Adminhtml_Model_Session')->getMessages()->getLastAddedMessage();
        if ($messages) {
            $success = 'error' != $messages->getType();
        }
        return $eventModel->setIsSuccess($success)->setInfo(__('Tax Rates Export'));
    }

    /**
     * Custom handler for sales archive operations
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchSalesArchiveManagement($config, $eventModel)
    {
        $request = Mage::app()->getRequest();
        $ids = $request->getParam('order_id', $request->getParam('order_ids'));
        if (is_array($ids)) {
            $ids = implode(', ', $ids);
        }
        return $eventModel->setInfo($ids);
    }

    /**
     * Custom handler for Recurring Profiles status update
     *
     * @param Magento_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchRecurringProfilesUpdate($config, $eventModel)
    {
        $message = '';
        $request = Mage::app()->getRequest();
        if ($request->getParam('action')) {
            $message .= ucfirst($request->getParam('action')) . ' action: ';
        }
        $message .= Mage::getSingleton('Magento_Adminhtml_Model_Session')->getMessages()->getLastAddedMessage()->getCode();
        return $eventModel->setInfo($message);
    }

    /**
     * Log tax class event
     * @param string $classType
     * @param Enterprise_Logging_Model_Event $eventModel
     * @param int $classId
     *
     * @return Enterprise_Logging_Model_Event
     */
    protected function _logTaxClassEvent($classType, $eventModel, $classId)
    {
        if ($classType == 'PRODUCT') {
            $eventModel->setEventCode('tax_product_tax_classes');
        }

        $success = true;
        $body = Mage::app()->getResponse()->getBody();
        $messages = $this->_objectManager->get('Magento_Core_Helper_Data')->jsonDecode($body);
        if (!empty($messages['success'])) {
            $success = $messages['success'];
            if (empty($classId) && !empty($messages['class_id'])) {
                $classId = $messages['class_id'];
            }
        }

        $messageInfo = $classType . ($classId ? ': #' . $classId : '');
        return $eventModel->setIsSuccess($success)->setInfo($messageInfo);
    }
}
