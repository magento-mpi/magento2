<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

abstract class Enterprise_Staging_Model_Staging_Adapter_Item_Abstract extends Enterprise_Staging_Model_Staging_Adapter_Abstract
{
    /**
     * proceed website scope tables
     *
     * @var mixed
     */
    static $_proceedWebsiteScopeTables = array();

    /**
     * processd store tables
     *
     * @var mixed
     */
    static $_proceedStoreScopeTables = array();

    /**
     * table copying exctule list
     *
     * @var mixed
     */
    protected $_excludeList = array(
        'core_store',
        'core_website',
        'eav_attribute',
        'eav_attribute_set',
        'eav_entity_type',
        'cms_page',
        'cms_block'
    );

    /**
     * table midel list
     *
     * @var mixed
     */
    protected $_tableModels = array(
       'product'            => 'catalog',
       'category'           => 'catalog',
       'customer'            => 'customer',
       'customer_address'    => 'customer',
    );

    /**
     * ignore table list
     *
     * @var mixed
     */
    protected $_ignoreTables = array(
        'category_flat'     => true,
        'product_flat'      => true
    );

    /**
     * EAV table types
     *
     * @var mixed
     */
    protected $_eavTableTypes = array('int', 'decimal', 'varchar', 'text', 'datetime');

    /**
     * event state code
     *
     * @var string
     */
    protected $_eventStateCode;

    /**
     * src model name
     *
     * @var string
     */
    protected $_srcModel;

    /**
     * target model name
     *
     * @var string
     */
    protected $_targetModel;



    /**
     * create Items
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param Mage_Core_Model_Abstract $object
     * @param Simple_Xml $itemXmlConfig
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    public function createItem(Enterprise_Staging_Model_Staging $staging, Mage_Core_Model_Abstract $object, $itemXmlConfig)
    {
        $this->_processItemMethodCallback('_createItemTableData', $staging, $itemXmlConfig, $object);

        return $this;
    }

    /**
     * Create item table, run website and item table structure  
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param string $targetTable
     * @param mixed $usedStorageMethod
     * @param Enterprise_Staging_Model_State_Abstract $object
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _createItemTableData($staging, $srcModel, $srcTable, $targetModel, $targetTable, $usedStorageMethod, $object)
    {
        if ($object instanceof Enterprise_Staging_Model_Staging_Website) {
            if (isset(self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable])) {
                return $this;
            }
        } elseif ($object instanceof Enterprise_Staging_Model_Staging_Store) {
            if (isset(self::$_proceedStoreScopeTables[$this->getEventStateCode()][$srcTable])) {
                return $this;
            }
        }

        $srcTableDesc = $this->getTableProperties($srcModel, $srcTable, true);
        $fields = $srcTableDesc['fields'];
        foreach ($fields as $id => $field) {
            if ((strpos($srcTable, 'catalog_product_website') === false)) {
                if ($field['extra'] == 'auto_increment') {
                    unset($fields[$id]);
                }
            }
        }
        $fields = array_keys($fields);

        if ($object instanceof Enterprise_Staging_Model_Staging_Website) {
            if (!in_array('website_id', $fields) && !in_array('website_ids', $fields) && !in_array('scope_id', $fields)) {
                return $this;
            }
            $this->_createWebsiteScopeItemTableData($staging, $object, $srcModel, $srcTable, $targetModel, $targetTable, $fields);
        } elseif ($object instanceof Enterprise_Staging_Model_Staging_Store) {
            if (!in_array('store_id', $fields) && !in_array('store_ids', $fields) && !in_array('scope_id', $fields)) {
                return $this;
            }
            $this->_createStoreScopeItemTableData($staging, $object, $srcModel, $srcTable, $targetModel, $targetTable, $fields);
        }

        return $this;
    }

    /**
     * Create item table, run website and item table structure  
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param string $targetTable
     * @param mixed $usedStorageMethod
     * @param Enterprise_Staging_Model_Website $website
     * @param mixed $fields 
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _createWebsiteScopeItemTableData($staging, $website, $srcModel, $srcTable, $targetModel, $targetTable, $fields)
    {
        $connection     = $this->getConnection($targetModel);

        $masterWebsite  = $website->getMasterWebsite();
        if (!$masterWebsite) {
            return $this;
        }
        $masterWebsiteId = $masterWebsite->getId();
        $slaveWebsiteId = $website->getSlaveWebsiteId();
        if (!$masterWebsiteId || !$slaveWebsiteId) {
            return $this;
        }

        $_updateField = end($fields);
        $destInsertSql = "INSERT INTO `{$srcTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$_updateField}=VALUES({$_updateField})";

        $_websiteFieldNameSql = 'website_id';
        foreach ($fields as $id => $field) {
            if ($field == 'website_id') {
                $fields[$id] = $slaveWebsiteId;
                $_websiteFieldNameSql = "{$field} = {$masterWebsiteId}";
            } elseif ($field == 'scope_id') {
                $fields[$id] = $slaveWebsiteId;
                $_websiteFieldNameSql = "scope = 'websites' AND {$field} = {$masterWebsiteId}";
            } elseif ($field == 'website_ids') {
                /* FIXME need to fix concat website_ids */
                $fields[$id] = "CONCAT(website_ids,',{$slaveWebsiteId}')";
                $_websiteFieldNameSql = "FIND_IN_SET({$masterWebsiteId},website_ids)";
            }
        }

        $srcSelectSql = $this->_getSimpleSelect($fields, $targetTable, $_websiteFieldNameSql);
        $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
        $connection->query($destInsertSql);
        self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }

    /**
     * Create item table, run website and item table structure  
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param string $targetTable
     * @param mixed $usedStorageMethod
     * @param Enterprise_Staging_Model_Store $store
     * @param mixed $fields 
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */    
    protected function _createStoreScopeItemTableData($staging, $store, $srcModel, $srcTable, $targetModel, $targetTable, $fields)
    {
        $connection     = $this->getConnection($targetModel);

        $masterStoreId  = $store->getMasterStoreId();
        $slaveStoreId   = $store->getSlaveStoreId();
        if (!$masterStoreId || !$slaveStoreId) {
            return $this;
        }

        $_updateField = end($fields);
        $destInsertSql = "INSERT INTO `{$srcTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$_updateField}=VALUES({$_updateField})";

        $_storeFieldNameSql = 'store_id';
        foreach ($fields as $id => $field) {
            if ($field == 'store_id') {
                $fields[$id] = $slaveStoreId;
                $_storeFieldNameSql = "({$field} = {$masterStoreId})"; //  OR {$field} = 0) TODO how about singlestore mode ?
            } elseif ($field == 'scope_id') {
                $fields[$id] = $slaveStoreId;
                $_storeFieldNameSql = "scope = 'stores' AND {$field} = {$masterStoreId}";
            }
        }

        $srcSelectSql = $this->_getSimpleSelect($fields, $targetTable, $_storeFieldNameSql);
        $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
        $connection->query($destInsertSql);
        self::$_proceedStoreScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }





    /**
     * run marge process
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param Enterprise_Staging_Model_Staging_Website $stagingWebsite
     * @param Simple_Xml $itemXmlConfig
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    public function mergeItem(Enterprise_Staging_Model_Staging $staging, Enterprise_Staging_Model_Staging_Website $stagingWebsite, $itemXmlConfig)
    {
        if ($staging->getIsMergeLater() == true) {
            return $this;
        }
        
        $this->_processItemMethodCallback('_mergeItemTableData', $staging, $itemXmlConfig, $stagingWebsite);

        return $this;
    }

    /**
     * Prepare data to merge as Website Scope and as Store scope 
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param string $targetTable
     * @param string $usedStorageMethod
     * @param Enterprise_Staging_Model_Staging_Website $stagingWebsite
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _mergeItemTableData($staging, $srcModel, $srcTable, $targetModel, $targetTable, $usedStorageMethod, $stagingWebsite)
    {
        $tableSrcDesc = $this->getTableProperties($srcModel, $srcTable, true);
        $fields = $tableSrcDesc['fields'];
        foreach ($fields as $id => $field) {
            if ((strpos($srcTable, 'catalog_product_website') === false)) {
                if ($field['extra'] == 'auto_increment') {
                    unset($fields[$id]);
                }
            }
        }
        $fields = array_keys($fields);

        if (!isset(self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable])) {
            $this->_mergeTableDataInWebsiteScope($staging, $stagingWebsite, $srcModel, $srcTable, $targetModel, $targetTable, $fields);
        }

        if (!isset(self::$_proceedStoreScopeTables[$this->getEventStateCode()][$srcTable])) {
            $this->_mergeTableDataInStoreScope($staging, $stagingWebsite, $srcModel, $srcTable, $targetModel, $targetTable, $fields);
        }

        return $this;
    }

    /**
     * process website scope 
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param string $targetTable
     * @param mixed $fields
     * @param Enterprise_Staging_Model_Staging_Website $stagingWebsite
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */    
    protected function _mergeTableDataInWebsiteScope($staging, $stagingWebsite, $srcModel, $srcTable, $targetModel, $targetTable, $fields)
    {
        if (!in_array('website_id', $fields) && !in_array('website_ids', $fields) && !in_array('scope_id', $fields)) {
            return $this;
        }
        $connection     = $this->getConnection($targetModel);

        $mapper         = $staging->getMapperInstance();
        /* @var $mapper Enterprise_Staging_Model_Staging_Mapper_Website */

        $slaveWebsite   = $stagingWebsite->getSlaveWebsite();
        $slaveWebsiteId = $slaveWebsite->getId();

        $mappedWebsites = $mapper->getUsedWebsites($slaveWebsiteId);
                
        if (in_array('website_ids', $fields)) {
            $this->_mergeTableDataInWebsiteScopeUpdate($connection, $mappedWebsites, $staging, $stagingWebsite, $srcModel, $srcTable, $targetModel, $targetTable, $fields);
        } else {
            $this->_mergeTableDataInWebsiteScopeInsert($connection, $mappedWebsites, $staging, $stagingWebsite, $srcModel, $srcTable, $targetModel, $targetTable, $fields);
        }
    }
    
    /**
     * Insert New data on merge
     *
     * @param Connection $connection
     * @param array $mappedWebsites
     * @param Enterprice_Staging_Model_Staging $staging
     * @param Enterprice_Staging_Model_Staging_Website $stagingWebsite
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param string $targetTable
     * @param array $fields
     * 
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _mergeTableDataInWebsiteScopeInsert($connection, $mappedWebsites, $staging, $stagingWebsite, $srcModel, $srcTable, $targetModel, $targetTable, $fields)
    {    
        $updateField = end($fields);

        foreach ($mappedWebsites['master_website'] as $masterWebsiteId => $slaveWebsiteId) {
            $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$updateField}=VALUES({$updateField})";

            $_websiteFieldNameSql = 'website_id';

            $_fields = $fields;
            $doInsert = true;
            foreach ($_fields as $id => $field) {
                if ($field == 'website_id') {
                    $_fields[$id] = $masterWebsiteId;
                    $_websiteFieldNameSql = "{$field} = {$slaveWebsiteId}";
                } elseif ($field == 'scope_id') {
                    $_fields[$id] = $masterWebsiteId;
                    $_websiteFieldNameSql = "scope = 'websites' AND {$field} = {$slaveWebsiteId}";
                } 
            }

            $srcSelectSql = $this->_getSimpleSelect($_fields, $srcTable, $_websiteFieldNameSql);
            $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
            $connection->query($destInsertSql);
        }
        self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }

    /**
     * Update data on merge
     *
     * @param Connection $connection
     * @param array $mappedWebsites
     * @param Enterprice_Staging_Model_Staging $staging
     * @param Enterprice_Staging_Model_Staging_Website $stagingWebsite
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param string $targetTable
     * @param array $fields
     * 
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */    
    protected function _mergeTableDataInWebsiteScopeUpdate($connection, $mappedWebsites, $staging, $stagingWebsite, $srcModel, $srcTable, $targetModel, $targetTable, $fields)
    {
        $updateField = end($fields);

        foreach ($mappedWebsites['master_website'] as $masterWebsiteId => $slaveWebsiteId) {
            $destInsertSql = "UPDATE `{$targetTable}` SET website_ids = IF(FIND_IN_SET({$masterWebsiteId},website_ids), website_ids, CONCAT(website_ids,',{$masterWebsiteId}')) 
                WHERE FIND_IN_SET({$slaveWebsiteId},website_ids)";
            $connection->query($destInsertSql);
        }

        self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }
        
            
    /**
     * Process Store scope 
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param string $targetTable
     * @param mixed $fields
     * @param Enterprise_Staging_Model_Staging_Website $stagingWebsite
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */    
    protected function _mergeTableDataInStoreScope($staging, $stagingWebsite, $srcModel, $srcTable, $targetModel, $targetTable, $fields)
    {
        if (!in_array('store_id', $fields) && !in_array('scope_id', $fields)) {
            return $this;
        }

        $connection     = $this->getConnection($targetModel);

        $mapper         = $staging->getMapperInstance();
        /* @var $mapper Enterprise_Staging_Model_Staging_Mapper_Website */

        $slaveWebsite   = $stagingWebsite->getSlaveWebsite();

        $storesMap      = $mapper->getSlaveToMasterStoreIds($slaveWebsite->getId());

        foreach ($storesMap as $masterWebsiteId => $toMasterStores) {
            foreach ($toMasterStores as $slaveStoreId => $masterStoreId) {
                $tableDestDesc = $this->getTableProperties($targetModel, $targetTable, true);

                $_updateField = end($fields);
                $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$_updateField}=VALUES({$_updateField})";
                $_storeFieldNameSql = 'store_id';
                $_fields = $fields;
                foreach ($fields as $id => $field) {
                    if ($field == 'store_id') {
                        $_fields[$id] = $masterStoreId;
                    } elseif ($field == 'scope_id') {
                        $_fields[$id] = $masterStoreId;
                        $_storeFieldNameSql = "scope = 'stores' AND {$field}";
                    }
                }
                $srcSelectSql = $this->_getSimpleSelect($_fields, $srcTable, "{$_storeFieldNameSql} = {$slaveStoreId}");
                $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
                $connection->query($destInsertSql);
            }
        }

        self::$_proceedStoreScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }










    /**
     * Staging Backup process
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param Simple_Xml $itemXmlConfig
     * @param mixed $syncData
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    public function backupItem(Enterprise_Staging_Model_Staging $staging, $itemXmlConfig, $syncData = false)
    {
        $this->_processItemMethodCallback('_backupItemTable', $staging, $itemXmlConfig);

        return $this;
    }
    
    /**
     * Prepare data for merging
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param string $targetTable
     * @param bool $usedStorageMethod
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _backupItemTable($staging, $srcModel, $srcTable, $targetModel, $targetTable, $usedStorageMethod)
    {
        $connection      = $this->getConnection($targetModel);

        $srcTableDesc    = $this->getTableProperties($srcModel, $srcTable);

        $internalPrefix = "";        
        
        $stateRegestryCode  = "staging/" . $this->getEventStateCode() . "/enterprise_staging/staging_event";
        
        $event = Mage::registry($stateRegestryCode);
        
        if (is_object($event)) {
            $internalPrefix = $event->getId();
        }
        
        $backupPrefix    = $this->getBackupTablePrefix($internalPrefix);
        
        $targetTable     = $this->getStagingTableName($staging, $srcModel, $srcTable, $backupPrefix, true);

        $targetTableDesc = $this->getTableProperties($targetModel, $targetTable);

        if (!$targetTableDesc) {
            $srcTableDesc['table_name'] = $targetTable;
            
            if (!empty($srcTableDesc['constraints'])) {
                foreach($srcTableDesc['constraints'] AS $constraint => $data) {
                    $srcTableDesc['constraints'][$constraint]['fk_name'] = $backupPrefix . $data['fk_name'];    
                }
            }
            $sql = $this->_getCreateSql($targetModel, $srcTableDesc, $staging);
            $connection->query($sql);
        }

        if ($srcTable != $targetTable) {
            $this->_backupItemTableData($staging, $srcModel, $srcTable, $targetModel, $targetTable);
        }

        return $targetTable;
    }

    /**
     * get backup table prefix
     *
     * @return string
     */
    public function getBackupTablePrefix($internalPrefix=null)
    {
        $backupPrefix    = Enterprise_Staging_Model_Staging_Config::getStagingBackupTablePrefix();
        
        if (isset($internalPrefix)) {
            $backupPrefix .= $internalPrefix . "_";
        }
        return $backupPrefix;
    }

    /**
     * process backup table data
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param string $targetTable
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _backupItemTableData($staging, $srcModel, $srcTable, $targetModel, $targetTable)
    {
        $srcTableDesc = $this->getTableProperties($srcModel, $srcTable, true);

        $fields = $srcTableDesc['fields'];
        $fields = array_keys($fields);

        if (!isset(self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable])) {
            $this->_backupWebsiteScopeItemTableData($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields);
        }

        if (!isset(self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable])) {
            $this->_backupStoreScopeItemTableData($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields);
        }

        return $this;
    }

    /**
     * process backup for website scope
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param string $targetTable
     * @param mixed $fields
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _backupWebsiteScopeItemTableData($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields)
    {
        if (!in_array('website_id', $fields) && !in_array('website_ids', $fields) && !in_array('scope_id', $fields)) {
            return $this;
        }
        
        $connection = $this->getConnection($srcModel);
        $connection->query("SET foreign_key_checks = 0;");
        
        $updateField = end($fields);
        $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s)";
        $srcSelectSql = $this->_getSimpleSelect($fields, $srcTable);
        $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
        $connection->query($destInsertSql);
        self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }

    /**
     * process backup for store scope
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param string $targetTable
     * @param mixed $fields
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _backupStoreScopeItemTableData($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields)
    {
        if (!in_array('store_id', $fields) && !in_array('store_ids', $fields) && !in_array('scope_id', $fields)) {
            return $this;
        }

        $connection = $this->getConnection($srcModel);
        $field = end($fields);
        $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s)";
        $srcSelectSql = $this->_getSimpleSelect($fields, $srcTable);        
        $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
        $connection->query($destInsertSql);
        $connection->query("SET foreign_key_checks = 1;");
        self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }

    /**
     * process rollback items
     *
     * @param Mage_Core_Model_Abstract $object
     * @param Simple_Xml $itemXmlConfig
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    public function rollbackItem(Mage_Core_Model_Abstract $object, $itemXmlConfig)
    {
        $this->_processItemMethodCallback('_rollbackItemTableData', $object, $itemXmlConfig);
        
        return $this;
    }

    /**
     * prepare table data to rollback
     *
     * @param Mage_Core_Model_Abstract $object
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param string $targetTable
     * @param bool $usedStorageMethod
     * @param Enterprise_Staging_Model_Staging_Website $stagingWebsite
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _rollbackItemTableData($object, $srcModel, $srcTable, $targetModel, $targetTable, $usedStorageMethod, $stagingWebsite)    
    {
        $targetTable = $this->getStagingTableName($object, $srcModel, $srcTable);
        
        $tableSrcDesc = $this->getTableProperties($srcModel, $srcTable);
        
        if (!$tableSrcDesc) {
            throw Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Staging Table %s doesn\'t exists',$srcTable));
        }

        $fields = $tableSrcDesc['fields'];
        $primaryField = "";
        foreach ($fields as $id => $field) {
            if ((strpos($srcTable, 'catalog_product_website') === false)
            && (strpos($srcTable, 'catalog_product_enabled_index') === false)
            && (strpos($srcTable, 'catalog_category_product_index') === false)
            && (strpos($srcTable, 'checkout_agreement_store') === false)) {
                if ($field['extra'] == 'auto_increment') {
                    $primaryField = $id;
                }
            }
        }
        
        $fields = array_keys($fields);
        
        $internalPrefix = $object->getEventId();
        
        $backupPrefix    = $this->getBackupTablePrefix($internalPrefix);

        $backupTable = $this->getStagingTableName($object, $srcModel, $targetTable, $backupPrefix, true);

        $this->_rollbackTableDataInWebsiteScope($object, $backupTable, $targetTable , $fields, $primaryField);

        $this->_rollbackTableDataInStoreScope($object, $backupTable, $targetTable , $fields, $primaryField);

        return $this;
    }

    /**
     * process website rollback
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcTable
     * @param string $targetTable
     * @param mixed $fields
     * @param string $primaryField
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _rollbackTableDataInWebsiteScope($staging, $srcTable, $targetTable, $fields, $primaryField)
    {
        if (!in_array('website_id', $fields) && !in_array('website_ids', $fields) && !in_array('scope_id', $fields)) {
            return $this;
        }

        $targetModel    = 'enterprise_staging';
        $connection     = $this->getConnection($targetModel);

        $tableDestDesc = $this->getTableProperties($targetModel, $targetTable);
        
        if (!$tableDestDesc) {
            throw Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Staging Table %s doesn\'t exists',$targetTable));
        }
        $_updateField = end($fields);

        /* @var $mapper Enterprise_Staging_Model_Staging_Mapper_Website */
        $mapper         = $this->getStaging()->getMapperInstance();
        $mapperUsedWebSites   = $mapper->getAllUsedWebsites();

        if (!empty($mapperUsedWebSites)) {
            $masterWebsiteIds = array_keys($mapperUsedWebSites);
        }

        if (!empty($mapperUsedWebSites[$masterWebsiteIds[0]]['master_website'])) {
            $slaveWebsiteIds = array_keys($mapperUsedWebSites[$masterWebsiteIds[0]]['master_website']);
        }
        
        if (count($slaveWebsiteIds) > 0 && count($masterWebsiteIds) > 0 && !empty($slaveWebsiteIds[0])){

            $_websiteFieldNameSql = 'website_id';

            
            if (in_array('website_id', $fields)) {
                if (empty($primaryField)) {
                    $primaryField = 'website_id';
                }
                $_websiteFieldNameSql = " `{$srcTable}`.website_id IN (" . implode(", ", $masterWebsiteIds). ")";                
            } elseif (in_array('scope_id', $fields)) {
                if (empty($primaryField)) {
                    $primaryField = 'scope_id';
                }
                $_websiteFieldNameSql = "`{$srcTable}`.scope = 'website' AND `{$srcTable}`.scope_id IN (" . implode(", ", $masterWebsiteIds). ")";
            } elseif (in_array('website_ids', $fields)) {
                $whereFields = array();
                foreach($masterWebsiteIds AS $webId) {
                    $whereFields[] = "FIND_IN_SET($webId, `{$srcTable}`.website_ids)";
                }
                $_websiteFieldNameSql = implode(" OR " , $whereFields);
            }

            //1 - need remove all resords from web_site tables, which added via marging
            if (!empty($primaryField)) {
                $destDeleteSql = $this->_deleteDataByUniqKeys('UNIQUE', $targetTable, $masterWebsiteIds, $slaveWebsiteIds, $tableDestDesc['keys']);
                
                if (!empty($destDeleteSql)) {
                    $connection->query($destDeleteSql);
                }
                $destDeleteSql = $this->_deleteDataByUniqKeys('PRIMARY', $targetTable, $masterWebsiteIds, $slaveWebsiteIds, $tableDestDesc['keys']);
                if ($destDeleteSql) {
                    $connection->query($destDeleteSql);
                }                
            }

            //2 - copy old data from bk_ tables
            $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$_updateField}=VALUES({$_updateField})";

            $srcSelectSql = $this->_getSimpleSelect($fields, $srcTable, $_websiteFieldNameSql);
            $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
            $connection->query($destInsertSql);
        }
        return $this;
    }

    
    /**
     * delete rows by Unique fields
     *
     * @param string $targetTable
     * @param array $masterIds
     * @param array $slaveIds
     * @param array $key
     *
     * @return value
     */
    protected function _deleteDataByUniqKeys($type='UNIQUE', $targetTable, $masterIds, $slaveIds, $keys)
    {
        if (is_array($masterIds)) {
            $masterWhere = " IN (" . implode(", ", $masterIds). ") ";
        } else {
            $masterWhere = " = " . $masterIds;            
        }
        if (is_array($slaveIds)) {
            $slaveWhere = " IN (" . implode(", ", $slaveIds). ") ";
        } else {
            $slaveWhere = " = " . $slaveIds;            
        }
        
        foreach ($keys AS $keyName => $keyData) {
            
            if ($keyData['type'] == $type) {
                $_websiteFieldNameSql = array();
                foreach ($keyData['fields'] as $field) {
                    
                    if ($field == 'website_id' || $field == 'store_id') {
                        $_websiteFieldNameSql[] = " T1.{$field} $slaveWhere 
                            AND T2.{$field} $masterWhere ";
                            
                    } elseif ($field == 'scope_id') {
                        
                        $_websiteFieldNameSql[] = " T1.scope = 'website' AND T1.{$field} $slaveWhere
                            AND T2.{$field} $masterWhere ";
                            
                    } else { //website_ids is update data as rule, so it must be in backup.
                        
                        $_websiteFieldNameSql[] = "T1.$field = T2.$field";
                         
                    }
                }
                
                $sql = "DELETE T1.* FROM {$targetTable} as T1, {$targetTable} as T2 WHERE " . implode(" AND " , $_websiteFieldNameSql);
                return $sql;
                 
            }
        } 
        return "";
    }
    /**
     * process store scope rollback
     *
     * @param Enterprise_Staging_Model_Staging_Website $website
     * @param string $srcTable
     * @param string $targetTable
     * @param mixed $fields
     * @param string $primaryField
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _rollbackTableDataInStoreScope($website, $srcTable, $targetTable, $fields, $primaryField)
    {
        if (!in_array('store_id', $fields) && !in_array('scope_id', $fields)) {
            return $this;
        }

        $targetModel    = 'enterprise_staging';
        $connection     = $this->getConnection($targetModel);

        $mapper         = $this->getStaging()->getMapperInstance();
        $mapperUsedWebSites   = $mapper->getAllUsedWebsites();

        if (!empty($mapperUsedWebSites)) {
            $slaveWebsiteIds = array_keys($mapperUsedWebSites);
        }

        if (!empty($mapperUsedWebSites[$slaveWebsiteIds[0]]['stores'])) {
            $slaveToMasterStoreIds = $mapperUsedWebSites[$slaveWebsiteIds[0]]['stores'];
        } else {
            $slaveToMasterStoreIds = array();
        }

        foreach ($slaveToMasterStoreIds as $stagingStoreId => $toMasterStores) {
            foreach ($toMasterStores as $masterStoreId => $slaveStoreId) {
                if (!$slaveStoreId) {
                    continue;
                }

                $tableDestDesc = $this->getTableProperties($targetModel, $targetTable);
                if (!$tableDestDesc) {
                    throw Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Staging Table %s doesn\'t exists',$targetTable));
                }

                $_updateField = end($fields);

                $_storeFieldNameSql = 'store_id';
                $_fields = $fields;
                
                foreach ($fields as $id => $field) {
                    if ($field == 'store_id') {
                        if (empty($primaryField))
                            $primaryField = $field;
                    } elseif ($field == 'scope_id') {
                        if (empty($primaryField))
                            $primaryField = $field;
                        $primaryField = $field;
                        $_storeFieldNameSql = "scope = 'stores' AND `{$srcTable}`.{$field}";
                    }
                }                
                //1 - need remove all resords from stores tables, which added via marging
                if (!empty($primaryField)) {
                    $destDeleteSql = $this->_deleteDataByUniqKeys('UNIQUE', $targetTable, $masterStoreId, $slaveStoreId, $tableDestDesc['keys']);
                
                    if (!empty($destDeleteSql)) {
                        $connection->query($destDeleteSql);
                    }
                     
                    $destDeleteSql = $this->_deleteDataByUniqKeys('PRIMARY', $targetTable, $masterStoreId, $slaveStoreId, $tableDestDesc['keys']);
                    
                    if ($destDeleteSql) {
                        $connection->query($destDeleteSql);
                    }                        
                }

                //2 - refresh data by backup
                $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$_updateField}=VALUES({$_updateField})";

                $srcSelectSql = $this->_getSimpleSelect($_fields, $srcTable, "{$_storeFieldNameSql} = {$slaveStoreId}");
                $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
                $connection->query($destInsertSql);
            }
        }

        return $this;
    }


    /**
     * get target table name
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param bool $usedStorageMethod
     * @param Mage_Core_Model_Abstract $object
     * @return string
     */
    protected function _getTargetTableName($staging, $srcModel, $srcTable, $targetModel, $usedStorageMethod, $object)
    {
        if (!$usedStorageMethod) {
            $targetTable = $srcTable;
        } elseif ($usedStorageMethod == Enterprise_Staging_Model_Staging_Config::STORAGE_METHOD_PREFIX) {
            $targetTable = $this->getStagingTableName($object, $srcModel, $srcTable);
        } else {
            // TODO case for staging that use new db
            throw new Enterprise_Staging_Exception('Wrong Storage Method!');
        }

        if (!$this->tableExists($targetModel, $targetTable)) {
            throw Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Staging Table %s doesn\'t exists',$table));
        }

        return $targetTable;
    }

    /**
     * abstract method which prepare data for action and call correcpond collback method
     *
     * @param string $callbackMethod
     * @param Enterprise_Staging_Model_Staging $staging
     * @param Simple_Xml $itemXmlConfig
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _processItemMethodCallback($callbackMethod, $staging, $itemXmlConfig, $object = null)
    {
        if ((int)$itemXmlConfig->is_backend) {
            return $this;
        }

        $usedStorageMethod  = (string)  $itemXmlConfig->use_starage_method;
        if (!$usedStorageMethod) {
            $usedStorageMethod = Enterprise_Staging_Model_Staging_Config::getUsedStorageMethod();
        }

        $model  = (string) $itemXmlConfig->model;
        $tables = (array)  $itemXmlConfig->entities;

        $resourceName = (string) Mage::getConfig()->getNode("global/models/{$model}/resourceModel");
        $entityTables = (array) Mage::getConfig()->getNode("global/models/{$resourceName}/entities");

        $targetModel = 'enterprise_staging';

        foreach ($entityTables as $entityTableConfig) {
            $table = $entityTableConfig->getName();

            if ($tables) {
                if (!array_key_exists($table, $tables)) {
                    continue;
                }
            }
            $realTableName = $this->getTableName("{$model}/{$table}");

            if (isset($this->_tableModels[$table])) {
                foreach ($this->_eavTableTypes as $type) {
                    $_eavTypeTable = $realTableName . '_' . $type;
                    $targetTable = $this->_getTargetTableName($staging, $model, $_eavTypeTable, $targetModel, $usedStorageMethod, $object);
                    $this->{$callbackMethod}($staging, $model, $_eavTypeTable, $targetModel, $targetTable, $usedStorageMethod, $object);
                }
                // ignore main EAV entity table
                continue;
            }
            if (isset($this->_ignoreTables[$table])) {
                continue;
            }

            $targetTable = $this->_getTargetTableName($staging, $model, $realTableName, $targetModel, $usedStorageMethod, $object);

            $this->{$callbackMethod}($staging, $model, $realTableName, $targetModel, $targetTable, $usedStorageMethod, $object);
        }

        return $this;
    }

    /**
     * set event state code attribute
     *
     * @param string $code
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    public function setEventStateCode($code)
    {
        $this->_eventStateCode = $code;

        return $this;
    }

    /**
     * get event state code
     *
     * @return string
     */
    public function getEventStateCode()
    {
        return $this->_eventStateCode;
    }
    
    /**
     * abstract method, prepare simple select by given parameters
     *
     * @param mixed $fields
     * @param string $table
     * @param string $where
     * @return string
     */
    protected function _getSimpleSelect($fields, $table, $where=null)
    {
        if (is_array($fields)) {
            $fields = implode("," , $fields);
        }
        
        if (isset($where)) {
            $where = " WHERE " . $where;
        }

        return "SELECT $fields FROM `{$table}` $where";
    }

}