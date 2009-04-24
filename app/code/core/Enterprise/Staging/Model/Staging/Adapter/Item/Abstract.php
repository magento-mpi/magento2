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
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

abstract class Enterprise_Staging_Model_Staging_Adapter_Item_Abstract extends Enterprise_Staging_Model_Staging_Adapter_Abstract
{
    /**
     * proceed tables
     *
     * @var array
     */
    static $_proceedTables = array();

    /**
     * proceed website scope tables
     *
     * @var array
     */
    static $_proceedWebsiteScopeTables = array();

    /**
     * processd store tables
     *
     * @var array
     */
    static $_proceedStoreScopeTables = array();

    protected function allowToProceedInWebsiteScope($srcTable, $fields)
    {
        if (!isset(self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable])) {
            if (in_array('website_id', $fields) || in_array('website_ids', $fields) || in_array('scope_id', $fields)) {
                return true;
            }
        }
    }

    protected function allowToProceedInStoreScope($srcTable, $fields)
    {
        if (!isset(self::$_proceedStoreScopeTables[$this->getEventStateCode()][$srcTable])) {
            if (in_array('store_id', $fields) || in_array('store_ids', $fields) || in_array('scope_id', $fields)) {
                return true;
            }
        }
    }






    /**
     * Staging Create (Staging Item handle part)
     *
     * @param Enterprise_Staging_Model_Staging $staging
     *
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    public function create(Enterprise_Staging_Model_Staging $staging)
    {
        $this->_processItemMethodCallback('_createItem', $staging);

        return $this;
    }


    /**
     * Create item table and records, run processes in website and store scopes
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string    $srcModel
     * @param string    $srcTable
     * @param string    $targetModel
     * @param string    $targetTable
     * @param mixed     $usedStorageMethod
     *
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _createItem($staging, $srcModel, $srcTable, $targetModel, $targetTable, $usedStorageMethod)
    {
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

        if ($this->allowToProceedInWebsiteScope($srcTable, $fields)) {
            $this->_createWebsiteScopeItemTableData($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields);
        }

        if ($this->allowToProceedInStoreScope($srcTable, $fields)) {
            $this->_createStoreScopeItemTableData($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields);
        }

        return $this;
    }

    /**
     * Create item table, run website and item table structure
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string    $srcModel
     * @param string    $srcTable
     * @param string    $targetModel
     * @param string    $targetTable
     * @param mixed     $fields
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _createWebsiteScopeItemTableData($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields)
    {
        $connection         = $this->getConnection($targetModel);

        $masterWebsiteId    = $staging->getMasterWebsiteId();
        $stagingWebsiteId   = $staging->getStagingWebsiteId();
        if (!$masterWebsiteId || !$stagingWebsiteId) {
            return $this;
        }

        $_updateField = end($fields);
        $destInsertSql = "INSERT INTO `{$srcTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$_updateField}=VALUES({$_updateField})";

        $_websiteFieldNameSql = 'website_id';
        foreach ($fields as $id => $field) {
            if ($field == 'website_id') {
                $fields[$id] = $stagingWebsiteId;
                $_websiteFieldNameSql = "{$field} = {$masterWebsiteId}";
            } elseif ($field == 'scope_id') {
                $fields[$id] = $stagingWebsiteId;
                $_websiteFieldNameSql = "scope = 'websites' AND {$field} = {$masterWebsiteId}";
            } elseif ($field == 'website_ids') {
                $fields[$id] = "CONCAT(website_ids,',{$stagingWebsiteId}')";
                $_websiteFieldNameSql = "FIND_IN_SET({$masterWebsiteId},website_ids)";
            }
        }

        $srcSelectSql  = $this->_getSimpleSelect($fields, $targetTable, $_websiteFieldNameSql);
        $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

        $connection->query($destInsertSql);

        self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }

    /**
     * Create item table, run website and item table structure
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string    $srcModel
     * @param string    $srcTable
     * @param string    $targetModel
     * @param string    $targetTable
     * @param mixed     $fields
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _createStoreScopeItemTableData($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields)
    {
        $connection     = $this->getConnection($targetModel);
        $mapper         = $staging->getMapperInstance();
        $websites       = $mapper->getWebsites();

        $_updateField = end($fields);

        foreach ($websites as $website) {
            $stores = $website->getStores();
            foreach ($stores as $_idx => $store) {
                $masterStoreId  = $store->getMasterStoreId();
                $stagingStoreId = $store->getStagingStoreId();
                if (!$masterStoreId || !$stagingStoreId) {
                    return $this;
                }

                $destInsertSql = "INSERT INTO `{$srcTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$_updateField}=VALUES({$_updateField})";
                $_storeFieldNameSql = 'store_id';

                $_fields = $fields;
                foreach ($_fields as $id => $field) {
                    if ($field == 'store_id') {
                        $_fields[$id] = $stagingStoreId;
                        $_storeFieldNameSql = "({$field} = {$masterStoreId})";
                    } elseif ($field == 'scope_id') {
                        $_fields[$id] = $stagingStoreId;
                        $_storeFieldNameSql = "scope = 'stores' AND {$field} = {$masterStoreId}";
                    }
                }

                $srcSelectSql  = $this->_getSimpleSelect($_fields, $targetTable, $_storeFieldNameSql);
                $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

                $connection->query($destInsertSql);

                self::$_proceedStoreScopeTables[$this->getEventStateCode()][$srcTable] = true;
            }
        }

        return $this;
    }






    /**
     * Staging Backup (Staging Item handle part)
     *
     * @param Enterprise_Staging_Model_Staging $staging
     *
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    public function backup(Enterprise_Staging_Model_Staging $staging)
    {
        $this->_processItemMethodCallback('_backupItem', $staging);

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
    protected function _backupItem($staging, $srcModel, $srcTable, $targetModel, $targetTable, $usedStorageMethod)
    {
        $internalPrefix = "";
        $stateRegestryCode  = "staging/" . $this->getEventStateCode() . "/enterprise_staging/staging_event";
        $event = Mage::registry($stateRegestryCode);
        if (is_object($event)) {
            $internalPrefix = $event->getId();
        }
        $backupPrefix    = $this->getBackupTablePrefix($internalPrefix);
        $targetTable     = $this->getStagingTableName($staging, $srcModel, $srcTable, $backupPrefix, true);

        if ($srcTable == $targetTable) {
            return $this;
        }

        if (isset(self::$_proceedTables[$this->getEventStateCode()][$srcTable])) {
            return $this;
        }

        $srcTableDesc = $this->getTableProperties($srcModel, $srcTable, true);
        $fields = $srcTableDesc['fields'];
        $fields = array_keys($fields);

        $this->_checkCreateTable($staging, $targetModel, $targetTable, $srcTableDesc, $backupPrefix);
        $this->_backupItemData($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields);

        return $this;
    }

    /**
     * get backup table prefix
     *
     * @return string
     */
    public function getBackupTablePrefix($internalPrefix = null)
    {
        $backupPrefix = Enterprise_Staging_Model_Staging_Config::getStagingBackupTablePrefix();

        if (isset($internalPrefix)) {
            $backupPrefix .= $internalPrefix . "_";
        }
        return $backupPrefix;
    }

    /**
     * process backup
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param string $targetTable
     * @param mixed $fields
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _backupItemData($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields)
    {
        $this->getConnection()->query("SET foreign_key_checks = 0;");

        $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s)";
        $srcSelectSql  = $this->_getSimpleSelect($fields, $srcTable);
        $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
        echo "$destInsertSql<br /><br />";
        $this->getConnection()->query($destInsertSql);

        $this->getConnection()->query("SET foreign_key_checks = 1;");

        self::$_proceedTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }






    /**
     * Staging Merge (Staging Item handle part)
     *
     * @param Enterprise_Staging_Model_Staging $staging
     *
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    public function merge(Enterprise_Staging_Model_Staging $staging)
    {
        $this->_processItemMethodCallback('_mergeItem', $staging);

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
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _mergeItem($staging, $srcModel, $srcTable, $targetModel, $targetTable, $usedStorageMethod)
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

        if ($this->allowToProceedInWebsiteScope($srcTable, $fields)) {
            $this->_mergeTableDataInWebsiteScope($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields);
        }

        if ($this->allowToProceedInStoreScope($srcTable, $fields)) {
            $this->_mergeTableDataInStoreScope($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields);
        }

        return $this;
    }

    /**
     * process website scope
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string    $srcModel
     * @param string    $srcTable
     * @param string    $targetModel
     * @param string    $targetTable
     * @param mixed     $fields
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _mergeTableDataInWebsiteScope($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields)
    {
        $connection     = $this->getConnection($targetModel);
        $mapper         = $staging->getMapperInstance();
        $mappedWebsites = $mapper->getWebsites();

        if (in_array('website_ids', $fields)) {
            $this->_mergeTableDataInWebsiteScopeUpdate($staging, $mappedWebsites, $connection, $srcTable, $targetTable, $fields);
        } else {
            $this->_mergeTableDataInWebsiteScopeInsert($staging, $mappedWebsites, $connection, $srcTable, $targetTable, $fields);
        }
    }

    /**
     * Insert New data on merge
     *
     * @param Enterprice_Staging_Model_Staging $staging
     * @param array $mappedWebsites
     * @param Connection $connection
     * @param string $srcTable
     * @param string $targetTable
     * @param array $fields
     *
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _mergeTableDataInWebsiteScopeInsert($staging, $mappedWebsites, $connection, $srcTable, $targetTable, $fields)
    {
        $updateField = end($fields);

        foreach ($mappedWebsites as $stagingWebsiteId => $masterWebsiteIds) {
            if (empty($stagingWebsiteId) || empty($masterWebsiteIds)) {
                continue;
            }

            $_websiteFieldNameSql = 'website_id';

            foreach ($masterWebsiteIds as $masterWebsiteId) {
                if (empty($masterWebsiteId)) {
                    continue;
                }
                $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$updateField}=VALUES({$updateField})";

                $_fields = $fields;
                foreach ($_fields as $id => $field) {
                    if ($field == 'website_id') {
                        $_fields[$id] = $masterWebsiteId;
                        $_websiteFieldNameSql = "{$field} = {$stagingWebsiteId}";
                    } elseif ($field == 'scope_id') {
                        $_fields[$id] = $masterWebsiteId;
                        $_websiteFieldNameSql = "scope = 'websites' AND {$field} = {$stagingWebsiteId}";
                    }
                }

                $srcSelectSql = $this->_getSimpleSelect($_fields, $srcTable, $_websiteFieldNameSql);
                $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

                $connection->query($destInsertSql);
            }
        }

        self::$_proceedWebsiteScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }

    /**
     * Update data on merge
     *
     * @param Enterprice_Staging_Model_Staging $staging
     * @param array $mappedWebsites
     * @param Connection $connection
     * @param string $srcTable
     * @param string $targetTable
     * @param array $fields
     *
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _mergeTableDataInWebsiteScopeUpdate($staging, $mappedWebsites, $connection, $srcTable, $targetTable, $fields)
    {
        $updateField = end($fields);

        foreach ($mappedWebsites as $stagingWebsiteId => $masterWebsiteIds) {
            if (empty($stagingWebsiteId) || empty($masterWebsiteIds)) {
                continue;
            }
            foreach ($masterWebsiteIds as $masterWebsiteId) {
                if (empty($masterWebsiteId)) {
                    continue;
                }
                $destInsertSql = "UPDATE `{$targetTable}` SET website_ids = IF(FIND_IN_SET({$masterWebsiteId},website_ids), website_ids, CONCAT(website_ids,',{$masterWebsiteId}'))
                    WHERE FIND_IN_SET({$stagingWebsiteId},website_ids)";

                $connection->query($destInsertSql);
            }
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
     * @param mixed  $fields
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _mergeTableDataInStoreScope($staging, $srcModel, $srcTable, $targetModel, $targetTable, $fields)
    {
        $connection = $this->getConnection($targetModel);
        $mapper     = $staging->getMapperInstance();
        $storesMap  = $mapper->getStores();

        foreach ($storesMap as $stagingStoreId => $masterStoreIds) {
            foreach ($masterStoreIds as $masterStoreId) {
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
                $srcSelectSql = $this->_getSimpleSelect($_fields, $srcTable, "{$_storeFieldNameSql} = {$stagingStoreId}");
                $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

                $connection->query($destInsertSql);
            }
        }

        self::$_proceedStoreScopeTables[$this->getEventStateCode()][$srcTable] = true;

        return $this;
    }






    /**
     * Staging Rollback (Staging Item handle part)
     *
     * @param Enterprise_Staging_Model_Staging $staging
     *
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    public function rollback(Enterprise_Staging_Model_Staging $staging)
    {
        $this->_processItemMethodCallback('_rollbackItem', $staging);

        return $this;
    }

    /**
     * prepare table data to rollback
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string  $srcModel
     * @param string  $srcTable
     * @param string  $targetModel
     * @param string  $targetTable
     *
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _rollbackItem($staging, $srcModel, $srcTable, $targetModel, $targetTable)
    {
        $targetTable  = $this->getStagingTableName($staging, $srcModel, $srcTable);
        $srcTableDesc = $this->getTableProperties($srcModel, $srcTable);
        if (!$srcTableDesc) {
            throw Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Staging Table %s doesn\'t exists', $srcTable));
        }
        $fields = $srcTableDesc['fields'];
        $fields = array_keys($fields);

        $internalPrefix = $staging->getEventId();
        $backupPrefix   = $this->getBackupTablePrefix($internalPrefix);
        $backupTable    = $this->getStagingTableName($staging, $srcModel, $targetTable, $backupPrefix, true);

        if ($this->tableExists($srcModel, $backupTable)) {
            if ($this->allowToProceedInWebsiteScope($srcTable, $fields)) {
                $this->_rollbackTableDataInWebsiteScope($staging, $backupTable, $targetTable , $fields);
            }

            if ($this->allowToProceedInStoreScope($srcTable, $fields)) {
                $this->_rollbackTableDataInStoreScope($staging, $backupTable, $targetTable , $fields);
            }
        }

        return $this;
    }

    /**
     * process website rollback
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcTable
     * @param string $targetTable
     * @param mixed $fields
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _rollbackTableDataInWebsiteScope($staging, $srcTable, $targetTable, $fields)
    {
        $targetModel    = 'enterprise_staging';
        $connection     = $this->getConnection($targetModel);

        $tableDestDesc = $this->getTableProperties($targetModel, $targetTable);
        if (!$tableDestDesc) {
            throw Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Staging Table %s doesn\'t exists',$targetTable));
        }

        $_updateField = end($fields);

        $mapper = $staging->getMapperInstance();

        $mergedWebsites = $mapper->getWebsites();

        foreach ($mergedWebsites as $stagingWebsiteId => $masterWebsiteIds) {
            if (!empty($masterWebsiteIds)) {

                $_websiteFieldNameSql = 'website_id';

                if (in_array('website_id', $fields)) {
                    $_websiteFieldNameSql = " `{$srcTable}`.website_id IN (" . implode(", ", $masterWebsiteIds). ")";
                } elseif (in_array('scope_id', $fields)) {
                    $_websiteFieldNameSql = "`{$srcTable}`.scope = 'website' AND `{$srcTable}`.scope_id IN (" . implode(", ", $masterWebsiteIds). ")";
                } elseif (in_array('website_ids', $fields)) {
                    $whereFields = array();
                    foreach($masterWebsiteIds AS $webId) {
                        $whereFields[] = "FIND_IN_SET($webId, `{$srcTable}`.website_ids)";
                    }
                    $_websiteFieldNameSql = implode(" OR " , $whereFields);
                }

                //1 - need remove all resords from web_site tables, which added via marging
                if (!empty($tableDestDesc['keys'])) {
                    if (!empty($tableDestDesc['keys']['PRIMARY']) && !empty($tableDestDesc['keys']['PRIMARY']['fields'])) {
                        $primaryFields = $tableDestDesc['keys']['PRIMARY']['fields'];
                    } else {
                        $primaryFields = array();
                    }

                    $destDeleteSql = $this->_deleteDataByKeys('UNIQUE', 'website',$targetTable, $targetTable, $masterWebsiteIds, $stagingWebsiteId, $tableDestDesc['keys']);
                    if (!empty($destDeleteSql)) {
echo "$destDeleteSql<br /><br />";
                        $connection->query($destDeleteSql);
                    }


                    $additionalWhereCondition = $_websiteFieldNameSql;
                    if (in_array('website_id', $primaryFields) || in_array('scope_id', $primaryFields) || in_array('website_ids', $primaryFields)) {
                        $additionalWhereCondition = "";
                    }

                    $destDeleteSql = $this->_deleteDataByKeys('PRIMARY', 'website', $srcTable, $targetTable, $masterWebsiteIds, $stagingWebsiteId, $tableDestDesc['keys'], $additionalWhereCondition);
                    if ($destDeleteSql) {
                        //$connection->query($destDeleteSql);
                    }
                }

                //2 - copy old data from bk_ tables
                $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$_updateField}=VALUES({$_updateField})";

                $srcSelectSql = $this->_getSimpleSelect($fields, $srcTable, $_websiteFieldNameSql);
                $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
echo "$destDeleteSql<br /><br />";
                $connection->query($destInsertSql);
            }
        }
        return $this;
    }

    /**
     * process store scope rollback
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcTable
     * @param string $targetTable
     * @param mixed $fields
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _rollbackTableDataInStoreScope($staging, $srcTable, $targetTable, $fields)
    {
        $targetModel    = 'enterprise_staging';
        $connection     = $this->getConnection($targetModel);

        $mapper = $staging->getMapperInstance();

        $mergedStores = $mapper->getStores();

        foreach ($mergedStores as $stagingStoreId => $masterStoreIds) {
            if (empty($stagingStoreId) || empty($masterStoreIds)) {
                continue;
            }
            foreach ($masterStoreIds as $masterStoreId) {
                if (empty($masterStoreId)) {
                    continue;
                }

                $tableDestDesc = $this->getTableProperties($targetModel, $targetTable);
                if (!$tableDestDesc) {
                    throw Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Staging Table %s doesn\'t exists', $targetTable));
                }

                $_updateField = end($fields);

                $_storeFieldNameSql = "`{$srcTable}`.store_id";
                $_fields = $fields;

                foreach ($_fields as $id => $field) {
                    if ($field == 'store_id') {
                        $_fields[$id] = $masterStoreId;
                    } elseif ($field == 'scope_id') {
                        $_storeFieldNameSql = "`{$srcTable}`.scope = 'stores' AND `{$srcTable}`.{$field}";
                    }
                }

                //1 - need remove all resords from stores tables, which added via marging
                if (!empty($tableDestDesc['keys'])) {
                    if (!empty($tableDestDesc['keys']['PRIMARY']) && !empty($tableDestDesc['keys']['PRIMARY']['fields'])) {
                        $primaryFields = $tableDestDesc['keys']['PRIMARY']['fields'];
                    } else {
                        $primaryFields = array();
                    }

                    $destDeleteSql = $this->_deleteDataByKeys('UNIQUE', 'store', $srcTable, $targetTable, $stagingStoreId, $masterStoreId, $tableDestDesc['keys']);
                    if (!empty($destDeleteSql)) {
echo "$destDeleteSql<br /><br />";
                        $connection->query($destDeleteSql);
                    }

                    $additionalWhereCondition = "{$_storeFieldNameSql} = {$masterStoreId}";
                    if ( in_array('store_id' , $primaryFields) || in_array('scope_id' ,$primaryFields)) {
                        $additionalWhereCondition = "";
                    }

                    $destDeleteSql = $this->_deleteDataByKeys('PRIMARY', 'store', $srcTable, $targetTable, $masterStoreId, $stagingStoreId, $tableDestDesc['keys'], $additionalWhereCondition);
                    if ($destDeleteSql) {
                        //$connection->query($destDeleteSql);
                    }
                }

                //2 - refresh data by backup
                $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$_updateField}=VALUES({$_updateField})";

                $srcSelectSql = $this->_getSimpleSelect($_fields, $srcTable, "{$_storeFieldNameSql} = {$masterStoreId}");
                $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
echo "$destDeleteSql<br /><br />";
                $connection->query($destInsertSql);
            }
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
    protected function _deleteDataByKeys($type='UNIQUE', $scope='website', $srcTable, $targetTable, $masterIds, $slaveIds, $keys, $addidtionalWhereCondition=null)
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

                        $_websiteFieldNameSql[] = " (T1.scope = '{$scope}' OR T1.scope = '{$scope}s') AND T1.{$field} $slaveWhere
                            AND T2.{$field} $masterWhere ";

                    } else { //website_ids is update data as rule, so it must be in backup.

                        $_websiteFieldNameSql[] = "T1.$field = T2.$field";

                    }
                }

                $sql = "DELETE T1.* FROM {$targetTable} as T1, {$srcTable} as T2 WHERE " . implode(" AND " , $_websiteFieldNameSql);
                if (!empty($addidtionalWhereCondition)) {
                    $addidtionalWhereCondition = str_replace(array($srcTable, $targetTable), array("T2" , "T1") , $addidtionalWhereCondition);
                    $sql .= " AND " . $addidtionalWhereCondition;
                }
                return $sql;

            }
        }
        return "";
    }

    /**
     * get target table name
     *
     * @param Enterprise_Staging_Model_Staging $staging
     * @param string $srcModel
     * @param string $srcTable
     * @param string $targetModel
     * @param bool $usedStorageMethod
     * @return string
     */
    protected function _getTargetTableName($staging, $srcModel, $srcTable, $targetModel, $usedStorageMethod)
    {
        if (!$usedStorageMethod) {
            $targetTable = $srcTable;
        } elseif ($usedStorageMethod == Enterprise_Staging_Model_Staging_Config::STORAGE_METHOD_PREFIX) {
            $targetTable = $this->getStagingTableName($staging, $srcModel, $srcTable);
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
     * Prepares data for action and makes callback
     *
     * @param string $callbackMethod
     * @param Enterprise_Staging_Model_Staging  $staging
     *
     * @return Enterprise_Staging_Model_Staging_Adapter_Item_Abstract
     */
    protected function _processItemMethodCallback($callbackMethod, $staging)
    {
        $itemXmlConfig = $this->getConfig();

        if ((int)$itemXmlConfig->is_backend) {
            return $this;
        }

        $usedStorageMethod  = (string)  $itemXmlConfig->use_storage_method;
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

            if (isset($this->_eavModels[$table])) {
                foreach ($this->_eavTableTypes as $type) {
                    $_eavTypeTable = $realTableName . '_' . $type;
                    $targetTable = $this->_getTargetTableName($staging, $model, $_eavTypeTable, $targetModel, $usedStorageMethod);
                    $this->{$callbackMethod}($staging, $model, $_eavTypeTable, $targetModel, $targetTable, $usedStorageMethod);
                }
                // ignore main EAV entity table
                continue;
            }
            if (isset($this->_ignoreTables[$table])) {
                continue;
            }

            $targetTable = $this->_getTargetTableName($staging, $model, $realTableName, $targetModel, $usedStorageMethod);

            $this->{$callbackMethod}($staging, $model, $realTableName, $targetModel, $targetTable, $usedStorageMethod);
        }

        return $this;
    }

    /**
     * Prepare simple select by given parameters
     *
     * @param mixed $fields
     * @param string $table
     * @param string $where
     * @return string
     */
    protected function _getSimpleSelect($fields, $table, $where = null)
    {
        if (is_array($fields)) {
            $fields = implode(",", $fields);
        }

        if (isset($where)) {
            $where = " WHERE " . $where;
        }

        return "SELECT $fields FROM `{$table}` $where";
    }
}