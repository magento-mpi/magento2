<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Staging resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Staging extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialization of model
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_staging', 'staging_id');
    }

    /**
     * Add website name to select
     *
     * @param string $field
     * @param mixed $value
     * @param object $object
     * @return Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->joinLeft(
            array('site'=>$this->getTable('core_website')),
            "staging_website_id = site.website_id",
            array('name' => 'site.name')
        );
        return $select;
    }

    /**
     * Before save processing
     *
     * @param Varien_Object $object
     * @return Enterprise_Staging_Model_Resource_Staging
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setUpdatedAt($this->formatDate(time()));
        if (!$object->getId()) {
            $object->setCreatedAt($object->getUpdatedAt());
        }

        parent::_beforeSave($object);

        return $this;
    }

    /**
     * Save items
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Resource_Staging
     */
    public function saveItems($staging)
    {
        foreach ($staging->getItemsCollection() as $item) {
            $item->save();
        }

        return $this;
    }

    /**
     * Validate all object's attributes against configuration
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Resource_Staging
     */
    public function validate($staging)
    {
        return $this;
    }

    /**
     * Update specific attribute value (set new value back in given model)
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $attribute
     * @param mixed $value
     * @return Enterprise_Staging_Model_Resource_Staging
     */
    public function updateAttribute($staging, $attribute, $value)
    {
        if ($stagingId = (int)$staging->getId()) {
            $whereSql = array('staging_id = ?' =>$stagingId);
            $this->_getWriteAdapter()
                ->update($this->getMainTable(), array($attribute => $value), $whereSql);
            $staging->setData($attribute, $value);
        }
        return $this;
    }

    /**
     * Return websites with processing status
     *
     * @return array
     */
    public function getProcessingWebsites()
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()->from($this->getMainTable(), array('staging_website_id'))
            ->where("status = ?", Enterprise_Staging_Model_Staging_Config::STATUS_STARTED);
        return $adapter->fetchAll($select);
    }

    /**
     * get bool result if website in processing now
     *
     * @param int $currentWebsiteId
     * @return bool
     */
    public function isWebsiteInProcessing($currentWebsiteId)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), array('COUNT(*)'))
            ->where('status = ?', Enterprise_Staging_Model_Staging_Config::STATUS_STARTED)
            ->where('staging_website_id = ?', $currentWebsiteId);

        $result = (int) $adapter->fetchOne($select);
        if ($result > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Create Staging Website with all relatives
     *
     * @param object Enterprise_Staging_Model_Staging $staging
     * @param object Enterprise_Staging_Model_Staging_Event $event
     * @return Enterprise_Staging_Model_Resource_Staging
     */
    public function createRun($staging, $event = null)
    {
        Mage::getResourceModel('Enterprise_Staging_Model_Resource_Adapter_Website')
            ->createRun($staging, $event);

        Mage::getResourceModel('Enterprise_Staging_Model_Resource_Adapter_Group')
            ->createRun($staging, $event);

        Mage::getResourceModel('Enterprise_Staging_Model_Resource_Adapter_Store')
            ->createRun($staging, $event);

        Mage::getResourceModel('Enterprise_Staging_Model_Resource_Adapter_Item')
            ->createRun($staging, $event);

        $this->_processStagingItemsCallback('createRun', $staging, $event);

        return $this;
    }

    /**
     * Update Staging Website
     *
     * @param object Enterprise_Staging_Model_Staging $staging
     * @param object Enterprise_Staging_Model_Staging_Event $event
     * @return Enterprise_Staging_Model_Resource_Staging
     */
    public function updateRun($staging, $event = null)
    {
        Mage::getResourceModel('Enterprise_Staging_Model_Resource_Adapter_Website')
            ->updateRun($staging, $event);

        return $this;
    }

    /**
     * Run Backup default tables before merge
     *
     * @param object Enterprise_Staging_Model_Staging $staging
     * @param object Enterprise_Staging_Model_Staging_Event $event
     * @return Enterprise_Staging_Model_Resource_Staging
     */
    public function backupRun($staging, $event = null)
    {
        $this->_processStagingItemsCallback('backupRun', $staging, $event);

        Mage::getModel('Enterprise_Staging_Model_Staging_Action')->saveOnBackupRun($staging, $event);

        return $this;
    }

    /**
     * Collect all backup tables
     *
     * @param  Enterprise_Staging_Model_Staging $staging
     * @param  Enterprise_Staging_Model_Staging_Event|null $event
     * @return Enterprise_Staging_Model_Resource_Staging
     */
    public function collectBackupTables($staging, $event = null)
    {
        $this->_processStagingItemsCallback('getBackupTablesRun', $staging, $event);

        return $this;
    }

    /**
     * Run Staging Website Merge
     *
     * @param object Enterprise_Staging_Model_Staging $staging
     * @param object Enterprise_Staging_Model_Staging_Event $event
     * @return Enterprise_Staging_Model_Resource_Staging
     */
    public function mergeRun($staging, $event = null)
    {
        if ($staging->getIsMergeLater()) {
            return $this;
        }

        $this->_processStagingItemsCallback('mergeRun', $staging, $event);

        return $this;
    }

    /**
     * Run Staging Website Unschedule
     *
     * @param object Enterprise_Staging_Model_Staging $staging
     * @param object Enterprise_Staging_Model_Staging_Event $event
     * @return Enterprise_Staging_Model_Resource_Staging
     */
    public function unscheduleMergeRun($staging, $event = null)
    {
        return $this;
    }

    /**
     * Run Staging Website Reset
     *
     * @param object Enterprise_Staging_Model_Staging $staging
     * @param object Enterprise_Staging_Model_Staging_Event $event
     * @return Enterprise_Staging_Model_Resource_Staging
     */
    public function resetRun($staging, $event = null)
    {
        return $this;
    }

    /**
     * Run Staging Website Rollback
     *
     * @param object Enterprise_Staging_Model_Staging $staging
     * @param object Enterprise_Staging_Model_Staging_Event $event
     * @return Enterprise_Staging_Model_Resource_Staging
     */
    public function rollbackRun($staging, $event = null)
    {
        $this->_processStagingItemsCallback('rollbackRun', $staging, $event, true);
        return $this;
    }

    /**
     * Run validate backend tables for frontend usage within Staging Website navigation
     *
     * @param object Enterprise_Staging_Model_Staging $staging
     * @return Enterprise_Staging_Model_Resource_Staging
     */
    public function checkfrontendRun($staging)
    {
        if (!Mage::registry('staging/frontend_checked_started')) {
            Mage::register('staging/frontend_checked_started', true);

            $stagingItems = Mage::getSingleton('Enterprise_Staging_Model_Staging_Config')->getStagingItems();
            foreach ($stagingItems as $stagingItem) {
                if (!$stagingItem->is_backend) {
                    continue;
                }

                $adapter = $this->getItemAdapterInstanse($stagingItem);
                $adapter->checkfrontendRun($staging);

                if ($stagingItem->extends) {
                    foreach ($stagingItem->extends->children() as $extendItem) {
                        if (!$extendItem->is_backend) {
                            continue;
                        }
                        if ((string)$extendItem->use_storage_method !== 'table_prefix') {
                            continue;
                        }
                        $adapter = $this->getItemAdapterInstanse($extendItem);
                        $adapter->checkfrontendRun($staging);
                    }
                }
            }

            Mage::unregister('staging/frontend_checked_started');
            Mage::getSingleton('Mage_Core_Model_Session')->setData('staging_frontend_website_is_checked', true);
        }
        return $this;
    }

    /**
     * Retrieve item resource adapter instance
     *
     * @param Varien_Simplexml_Element $itemXmlConfig
     * @return Enterprise_Staging_Model_Resource_Adapter_Abstract
     */
    public function getItemAdapterInstanse($itemXmlConfig)
    {
        if ($itemXmlConfig) {
            $resourceAdapterName = (string) $itemXmlConfig->resource_adapter;
            if (!$resourceAdapterName) {
                $resourceAdapterName = 'Enterprise_Staging_Model_Resource_Adapter_Item_Default';
            }
            $resourceAdapter = Mage::getResourceModel($resourceAdapterName);
            if ($resourceAdapter) {
                $resourceAdapter->setConfig($itemXmlConfig);
                return $resourceAdapter;
            }
        }
        throw new Enterprise_Staging_Exception(Mage::helper('Enterprise_Staging_Helper_Data')->__('Wrong item resource adapter model.'));
    }

    /**
     * Process staging itemsCallback
     *
     * @param string $callback
     * @param Enterprise_Staging_Model_Staging $staging
     * @param Enterprise_Staging_Model_Staging_Event $event
     * @param boolean $ignoreExtends
     * @return Enterprise_Staging_Model_Resource_Staging
     */
    protected function _processStagingItemsCallback($callback, $staging, $event = null, $ignoreExtends = false)
    {
        $stagingItems = $staging->getMapperInstance()->getStagingItems();
        foreach ($stagingItems as $stagingItem) {
            $adapter = $this->getItemAdapterInstanse($stagingItem);
            $adapter->{$callback}($staging, $event);
            if ($ignoreExtends) {
                continue;
            }
            if ($stagingItem->extends) {
                foreach ($stagingItem->extends->children() as $extendItem) {
                    if (!Mage::getSingleton('Enterprise_Staging_Model_Staging_Config')->isItemModuleActive($extendItem)) {
                         continue;
                    }
                    $adapter = $this->getItemAdapterInstanse($extendItem);
                    $adapter->{$callback}($staging, $event);
                }
            }
        }
        return $this;
    }
}
