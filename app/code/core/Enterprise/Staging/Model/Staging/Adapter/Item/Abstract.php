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
    static $_proceedWebsiteScopeTables = array();

    static $_proceedStoreScopeTables = array();

    protected $_excludeList = array(
        'core_store',
        'core_website',
        'eav_attribute',
        'eav_attribute_set',
        'eav_entity_type',
        'cms_page',
        'cms_block'
    );

    protected $_tableModels = array(
       'product'            => 'catalog',
       'category'           => 'catalog',
       'customer'            => 'customer',
       'customer_address'    => 'customer',
    );

    protected $_ignoreTables = array(
        'category_flat'     => true,
        'product_flat'      => true
    );

    protected $_eavTableTypes = array('int', 'decimal', 'varchar', 'text', 'datetime');

    protected $_eventStateCode;

    protected $_srcModel;

    protected $_targetModel;










    public function createItem(Enterprise_Staging_Model_Staging $staging, Mage_Core_Model_Abstract $object, $itemXmlConfig)
    {
        $this->_processItemMethodCallback('_createItemTableData', $staging, $itemXmlConfig, $object);

        return $this;
    }

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

        $srcSelectSql = "SELECT ".implode(',',$fields)." FROM `{$targetTable}` WHERE {$_websiteFieldNameSql}";
        $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

        //echo $destInsertSql.'<br /><br /><br /><br />';
        $connection->query($destInsertSql);

        self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }

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

        $srcSelectSql = "SELECT ".implode(',',$fields)." FROM `{$targetTable}` WHERE {$_storeFieldNameSql}";
        $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

        //echo $destInsertSql.'<br /><br /><br /><br />';
        $connection->query($destInsertSql);

        self::$_proceedStoreScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }





    public function mergeItem(Enterprise_Staging_Model_Staging $staging, Enterprise_Staging_Model_Staging_Website $stagingWebsite, $itemXmlConfig)
    {
        if ($staging->getIsMergeLater() == true) {
            return $this;
        }
        
        $this->_processItemMethodCallback('_mergeItemTableData', $staging, $itemXmlConfig, $stagingWebsite);

        return $this;
    }

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

        $updateField = end($fields);

        foreach ($mappedWebsites['master_website'] as $masterWebsiteId => $slaveWebsiteId) {
            $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$updateField}=VALUES({$updateField})";

            $_websiteFieldNameSql = 'website_id';

            $_fields = $fields;
            foreach ($_fields as $id => $field) {
                if ($field == 'website_id') {
                    $_fields[$id] = $masterWebsiteId;
                    $_websiteFieldNameSql = "{$field} = {$slaveWebsiteId}";
                } elseif ($field == 'scope_id') {
                    $_fields[$id] = $masterWebsiteId;
                    $_websiteFieldNameSql = "scope = 'websites' AND {$field} = {$slaveWebsiteId}";
                } elseif ($field == 'website_ids') {
                    /* FIXME need to fix concat website_ids */
                    $_fields[$id] = "CONCAT(website_ids,',{$masterWebsiteId}')";
                    $_websiteFieldNameSql = "FIND_IN_SET({$slaveWebsiteId},website_ids)";
                }
            }

            $srcSelectSql = "SELECT ".implode(',',$_fields)." FROM `{$srcTable}` WHERE {$_websiteFieldNameSql}";

            $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
            //echo $destInsertSql.'<br /><br /><br /><br />';
            $connection->query($destInsertSql);
        }

        self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }

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

                $srcSelectSql = "SELECT ".implode(',',$_fields)." FROM `{$srcTable}` WHERE {$_storeFieldNameSql} = {$slaveStoreId}";
                $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

                //echo $destInsertSql.'<br /><br /><br /><br />';
                $connection->query($destInsertSql);
            }
        }

        self::$_proceedStoreScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }










    public function backupItem(Enterprise_Staging_Model_Staging $staging, $itemXmlConfig, $syncData = false)
    {
        $this->_processItemMethodCallback('_backupItemTable', $staging, $itemXmlConfig);

        return $this;
    }
    
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

            //echo '<pre>';
            //echo $sql;
            //echo '</pre>';
            //echo '<br>';

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

    protected function _backupWebsiteScopeItemTableData($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields)
    {
        if (!in_array('website_id', $fields) && !in_array('website_ids', $fields) && !in_array('scope_id', $fields)) {
            return $this;
        }

        $connection = $this->getConnection($srcModel);

        $updateField = end($fields);

        $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$updateField}=VALUES({$updateField})";

        $srcSelectSql = "SELECT ".implode(',',$fields)." FROM `{$srcTable}`";

        $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

        //echo $destInsertSql.'<br /><br /><br /><br />';
        $connection->query($destInsertSql);

        self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }

    protected function _backupStoreScopeItemTableData($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields)
    {
        if (!in_array('store_id', $fields) && !in_array('store_ids', $fields) && !in_array('scope_id', $fields)) {
            return $this;
        }

        $connection = $this->getConnection($srcModel);

        $field = end($fields);

        $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$field}=VALUES({$field})";

        $srcSelectSql = "SELECT ".implode(',',$fields)." FROM `{$srcTable}`";

        $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

        //echo $destInsertSql.'<br /><br /><br /><br />';
        $connection->query($destInsertSql);

        self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }

    public function rollbackItem(Mage_Core_Model_Abstract $object, $itemXmlConfig)
    {
        $this->_processItemMethodCallback('_rollbackItemTableData', $object, $itemXmlConfig);
        
        return $this;
    }

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
            $slaveWebsiteIds = array_keys($mapperUsedWebSites);
        }

        if (!empty($mapperUsedWebSites[$slaveWebsiteIds[0]]['master_website'])) {
            $masterWebsiteIds = array_values($mapperUsedWebSites[$slaveWebsiteIds[0]]['master_website']);
        }

        if (count($slaveWebsiteIds) > 0 && count($masterWebsiteIds) > 0 && !empty($slaveWebsiteIds[0])){

            $_websiteFieldNameSql = 'website_id';

            $_fields = $fields;
            foreach ($_fields as $id => $field) {
                if ($field == 'website_id') {
                    //$_fields[$id] = $slaveWebsiteIds[0]; // - no need to redeclare field, just copy it!
                    $_websiteFieldNameSql = " `{$srcTable}`.{$field} IN (" . implode(", ", $masterWebsiteIds). ")";
                } elseif ($field == 'scope_id') {
                    $_websiteFieldNameSql = "`{$srcTable}`.scope = 'website' AND `{$srcTable}`.{$field} IN (" . implode(", ", $masterWebsiteIds). ")";
                } elseif ($field == 'website_ids') {
                    $whereFields = array();
                    foreach($masterWebsiteIds AS $webId) {
                        $whereFields[] = "FIND_IN_SET($webId, `{$srcTable}`.website_ids)";
                    }
                    $_websiteFieldNameSql = implode(" OR " , $whereFields);
                }
            }

            //1 - need remove all resords from web_site tables, which added via marging
            if (!empty($primaryField)) {
                $destDeleteSql = "
                    DELETE {$targetTable}.* FROM `{$srcTable}`, `{$targetTable}`
                    WHERE `{$targetTable}`.$primaryField = `{$srcTable}`.$primaryField
                        AND $_websiteFieldNameSql";
                //echo $destDeleteSql.'<br /><br /><br /><br />';
                $connection->query($destDeleteSql);
            }

            //2 - copy old data from bk_ tables
            $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$_updateField}=VALUES({$_updateField})";

            $srcSelectSql = "SELECT ".implode(',',$_fields)." FROM `{$srcTable}` WHERE {$_websiteFieldNameSql}";

            $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
            //echo $destInsertSql.'<br /><br /><br /><br />';
            $connection->query($destInsertSql);
        }
        return $this;
    }

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
                    } elseif ($field == 'scope_id') {
                        $_storeFieldNameSql = "scope = 'stores' AND `{$srcTable}`.{$field}";
                    }
                }                
                //1 - need remove all resords from stores tables, which added via marging
                if (!empty($primaryField)) {
                    $destDeleteSql = "
                        DELETE {$targetTable}.* FROM `{$srcTable}`, `{$targetTable}`
                        WHERE `{$targetTable}`.$primaryField = `{$srcTable}`.$primaryField
                            AND `{$srcTable}`.{$_storeFieldNameSql} = {$slaveStoreId}";
                    //echo $destDeleteSql.'<br /><br /><br /><br />';
                    $connection->query($destDeleteSql);
                }

                //2 - refresh data by backup
                $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$_updateField}=VALUES({$_updateField})";

                $srcSelectSql = "SELECT ".implode(',',$_fields)." FROM `{$srcTable}` WHERE {$_storeFieldNameSql} = {$slaveStoreId}";

                $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
                //echo $destInsertSql.'<br /><br />store<br /><br />';
                $connection->query($destInsertSql);
            }
        }

        return $this;
    }


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

    public function setEventStateCode($code)
    {
        $this->_eventStateCode = $code;

        return $this;
    }

    public function getEventStateCode()
    {
        return $this->_eventStateCode;
    }

}