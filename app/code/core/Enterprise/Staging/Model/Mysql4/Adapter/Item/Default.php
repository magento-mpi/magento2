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

class Enterprise_Staging_Model_Mysql4_Adapter_Item_Default extends Enterprise_Staging_Model_Mysql4_Adapter_Abstract
{
    /**
     * proceed tables
     *
     * @var array
     */
    protected $_proceedTables = array();

    /**
     * Return Staging table name with all prefixes
     *
     * @param string $model
     * @param string $table
     * @param string $internalPrefix
     * @param bool $ignoreIsStaging
     * @return string
     */
    public function getStagingTableName($model, $table, $internalPrefix = '', $ignoreIsStaging = false)
    {
        if (isset($this->_proceedTables[$table])) {
            return $this->_proceedTables[$table];
        }
        return parent::getStagingTableName($model, $table, $internalPrefix, $ignoreIsStaging);
    }

    /**
     * Check backend Staging Tables Creates
     *
     * @param   object Enterprise_Staging_Model_Staging $staging
     *
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    public function checkfrontend(Enterprise_Staging_Model_Staging $staging)
    {
        parent::checkfrontend($staging);
        $this->_processItemMethodCallback('_checkBackendTables');
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param   string $model
     * @param   string $table
     * @param   string $srcTable
     * @param   string $targetTable
     *
     * @return  Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    protected function _checkBackendTables($model, $table, $srcTable, $targetTable)
    {
        $targetTable            = Enterprise_Staging_Model_Staging_Config::getTablePrefix() . $srcTable;
        $srcTableDescription    = $this->getTableProperties($srcTable, $model);
        $targetTableDescription = $this->getTableProperties($targetTable);

        if ($srcTableDescription && !$targetTableDescription) {
            $this->createTable($model, $srcTableDescription, $targetTable);
        }

        $this->_proceedTables[$srcTable] = $targetTable;

        return $this;
    }

    /**
     * Staging Create (Staging Item handle part)
     *
     * @param   object Enterprise_Staging_Model_Staging $staging
     * @param   object Enterprise_Staging_Model_Staging_Event $event
     *
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    public function create(Enterprise_Staging_Model_Staging $staging, $event = null)
    {
        parent::checkfrontend($staging, $event);
        $this->_processItemMethodCallback('_createItem');
        return $this;
    }


    /**
     * Create item table and records, run processes in website and store scopes
     *
     * @param string    $model
     * @param string    $table
     * @param string    $srcTable
     * @param string    $targetTable
     *
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    protected function _createItem($model, $table, $srcTable, $targetTable)
    {
        $srcTableDesc = $this->getTableProperties($srcTable, $model);
        if (!$srcTableDesc) {
            return $this;
        }

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
            $this->_createWebsiteScopeItemTableData($model, $srcTable, $targetTable, $fields);
        }

        if ($this->allowToProceedInStoreScope($srcTable, $fields)) {
            $this->_createStoreScopeItemTableData($model, $srcTable, $targetTable, $fields);
        }

        $this->_proceedTables[$srcTable] = $targetTable;

        return $this;
    }

    /**
     * Create item table, run website and item table structure
     *
     * @param string    $model
     * @param string    $srcTable
     * @param string    $targetTable
     * @param mixed     $fields
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    protected function _createWebsiteScopeItemTableData($model, $srcTable, $targetTable, $fields)
    {
        $staging            = $this->getStaging();
        $connection         = $this->getConnection();

        $masterWebsiteId    = (int) $staging->getMasterWebsiteId();
        $stagingWebsiteId   = (int) $staging->getStagingWebsiteId();
        if (!$masterWebsiteId || !$stagingWebsiteId) {
            return $this;
        }

        $_updateField = end($fields);

        if (in_array('website_ids', $fields)) {
            $destInsertSql = "UPDATE `{$srcTable}` SET `website_ids` = IF(FIND_IN_SET({$stagingWebsiteId},`website_ids`), `website_ids`, CONCAT(`website_ids`,',{$stagingWebsiteId}'))
                    WHERE FIND_IN_SET({$masterWebsiteId},`website_ids`)";
        } else {
            $destInsertSql = "INSERT INTO `{$srcTable}` (".$this->_prepareFields($fields).") (%s) ON DUPLICATE KEY UPDATE `{$_updateField}`=VALUES(`{$_updateField}`)";

            $_websiteFieldNameSql = 'website_id';
            foreach ($fields as $id => $field) {
                if ($field == 'website_id') {
                    $fields[$id] = $stagingWebsiteId;
                    $_websiteFieldNameSql = "`{$field}` = {$masterWebsiteId}";
                } elseif ($field == 'scope_id') {
                    $fields[$id] = $stagingWebsiteId;
                    $_websiteFieldNameSql = "scope = 'websites' AND `{$field}` = {$masterWebsiteId}";
                } elseif ($field == 'website_ids') {
                    $fields[$id] = new Zend_Db_Expr("CONCAT(website_ids,',{$stagingWebsiteId}')");
                    $_websiteFieldNameSql = "FIND_IN_SET({$masterWebsiteId},website_ids)";
                }
            }

            $srcSelectSql  = $this->_getSimpleSelect($fields, $targetTable, $_websiteFieldNameSql);
            $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
        }

        $connection->query($destInsertSql);

        return $this;
    }

    /**
     * Create item table, run website and item table structure
     *
     * @param string    $model
     * @param string    $srcTable
     * @param string    $targetTable
     * @param mixed     $fields
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    protected function _createStoreScopeItemTableData($model, $srcTable, $targetTable, $fields)
    {
        $staging        = $this->getStaging();
        $connection     = $this->getConnection();
        $websites       = $staging->getMapperInstance()->getWebsites();

        if (!empty($websites)) {
            $_updateField = end($fields);
            foreach ($websites as $website) {
                $stores = $website->getStores();
                foreach ($stores as $_idx => $store) {
                    $masterStoreId  = (int) $store->getMasterStoreId();
                    $stagingStoreId = (int) $store->getStagingStoreId();
                    if (!$masterStoreId || !$stagingStoreId) {
                        return $this;
                    }

                    $destInsertSql = "INSERT INTO `{$srcTable}` (".$this->_prepareFields($fields).") (%s) ON DUPLICATE KEY UPDATE `{$_updateField}`=VALUES(`{$_updateField}`)";
                    $_storeFieldNameSql = 'store_id';

                    $_fields = $fields;
                    foreach ($_fields as $id => $field) {
                        if ($field == 'store_id') {
                            $_fields[$id] = $stagingStoreId;
                            $_storeFieldNameSql = "({$field} = {$masterStoreId})";
                        } elseif ($field == 'scope_id') {
                            $_fields[$id] = $stagingStoreId;
                            $_storeFieldNameSql = "`scope` = 'stores' AND `{$field}` = {$masterStoreId}";
                        }
                    }

                    $srcSelectSql  = $this->_getSimpleSelect($_fields, $targetTable, $_storeFieldNameSql);
                    $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

                    $connection->query($destInsertSql);
                }
            }
        }
        return $this;
    }






    /**
     * Staging Backup (Staging Item handle part)
     *
     * @param   object Enterprise_Staging_Model_Staging $staging
     * @param   object Enterprise_Staging_Model_Staging_Event $event
     *
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    public function backup(Enterprise_Staging_Model_Staging $staging, $event = null)
    {
        parent::backup($staging, $event);
        $this->_processItemMethodCallback('_backupItem');
        return $this;
    }

    /**
     * Prepare data for merging
     *
     * @param string $model
     * @param string $table
     * @param string $srcTable
     * @param string $targetTable
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    protected function _backupItem($model, $table, $srcTable, $targetTable)
    {
        $internalPrefix  = $this->getEvent()->getId();

        $backupPrefix    = $this->getBackupTablePrefix($internalPrefix);
        $targetTable     = $this->getStagingTableName($model, $srcTable, $backupPrefix, true);

        if ($srcTable != $targetTable) {
            $srcTableDesc = $this->getTableProperties($srcTable, $model);
            if ($srcTableDesc) {
                $fields = $srcTableDesc['fields'];
                $fields = array_keys($fields);

                $this->_checkCreateTable($model, $srcTableDesc, $targetTable, $backupPrefix);
                $this->_backupItemData($model, $srcTable, $targetTable, $fields);
            }
        }

        $this->_proceedTables[$srcTable] = $targetTable;

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
            $backupPrefix .= $internalPrefix;
        }
        return $backupPrefix . "_";;
    }

    /**
     * process backup
     *
     * @param string $model
     * @param string $srcTable
     * @param string $targetTable
     * @param mixed $fields
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    protected function _backupItemData($model, $srcTable, $targetTable, $fields)
    {
        $this->getConnection()->query("SET foreign_key_checks = 0;");

        $destInsertSql = "INSERT INTO `{$targetTable}` (".$this->_prepareFields($fields).") (%s)";
        $srcSelectSql  = $this->_getSimpleSelect($fields, $srcTable);
        $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

        $this->getConnection()->query($destInsertSql);

        $this->getConnection()->query("SET foreign_key_checks = 1;");

        return $this;
    }






    /**
     * Staging Merge (Staging Item handle part)
     *
     * @param   object Enterprise_Staging_Model_Staging $staging
     * @param   object Enterprise_Staging_Model_Staging_Event $event
     *
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    public function merge(Enterprise_Staging_Model_Staging $staging, $event = null)
    {
        parent::merge($staging, $event);
        $this->_processItemMethodCallback('_mergeItem');
        return $this;
    }

    /**
     * Prepare data to merge as Website Scope and as Store scope
     *
     * @param string $model
     * @param string $table
     * @param string $srcTable
     * @param string $targetTable
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    protected function _mergeItem($model, $table, $srcTable, $targetTable)
    {
        $srcTableDesc = $this->getTableProperties($srcTable, $model);
        if (!$srcTableDesc) {
            return $this;
        }

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
            $this->_mergeTableDataInWebsiteScope($model, $srcTable, $targetTable, $fields);
        }

        if ($this->allowToProceedInStoreScope($srcTable, $fields)) {
            $this->_mergeTableDataInStoreScope($model, $srcTable, $targetTable, $fields);
        }

        $this->_proceedTables[$srcTable] = $targetTable;

        return $this;
    }

    /**
     * process website scope
     *
     * @param string    $model
     * @param string    $srcTable
     * @param string    $targetTable
     * @param mixed     $fields
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    protected function _mergeTableDataInWebsiteScope($model, $srcTable, $targetTable, $fields)
    {
        $staging        = $this->getStaging();
        $connection     = $this->getConnection($model);
        $mappedWebsites = $staging->getMapperInstance()->getWebsites();

        if (in_array('website_ids', $fields)) {
            $this->_mergeTableDataInWebsiteScopeUpdate($mappedWebsites, $connection, $srcTable, $targetTable, $fields);
        } else {
            $this->_mergeTableDataInWebsiteScopeInsert($mappedWebsites, $connection, $srcTable, $targetTable, $fields);
        }

        return $this;
    }

    /**
     * Insert New data on merge
     *
     * @param array $mappedWebsites
     * @param Connection $connection
     * @param string $srcTable
     * @param string $targetTable
     * @param array $fields
     *
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    protected function _mergeTableDataInWebsiteScopeInsert($mappedWebsites, $connection, $srcTable, $targetTable, $fields)
    {
        $updateField = end($fields);

        foreach ($mappedWebsites as $stagingWebsiteId => $masterWebsiteIds) {
            if (empty($stagingWebsiteId) || empty($masterWebsiteIds)) {
                continue;
            }
            $stagingWebsiteId = intval($stagingWebsiteId);

            $_websiteFieldNameSql = 'website_id';

            foreach ($masterWebsiteIds as $masterWebsiteId) {
                if (empty($masterWebsiteId)) {
                    continue;
                }
                $masterWebsiteId = intval($masterWebsiteId);

                $destInsertSql = "INSERT INTO `{$targetTable}` (".$this->_prepareFields($fields).") (%s) ON DUPLICATE KEY UPDATE `{$updateField}`=VALUES(`{$updateField}`)";

                $_fields = $fields;
                foreach ($_fields as $id => $field) {
                    if ($field == 'website_id') {
                        $_fields[$id] = $masterWebsiteId;
                        $_websiteFieldNameSql = "{$field} = {$stagingWebsiteId}";
                    } elseif ($field == 'scope_id') {
                        $_fields[$id] = $masterWebsiteId;
                        $_websiteFieldNameSql = "`scope` = 'websites' AND `{$field}` = {$stagingWebsiteId}";
                    }
                }

                $srcSelectSql = $this->_getSimpleSelect($_fields, $srcTable, $_websiteFieldNameSql);
                $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

                $connection->query($destInsertSql);
            }
        }

        return $this;
    }

    /**
     * Update data on merge
     *
     * @param array         $mappedWebsites
     * @param Connection    $connection
     * @param string        $srcTable
     * @param string        $targetTable
     * @param array         $fields
     *
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    protected function _mergeTableDataInWebsiteScopeUpdate($mappedWebsites, $connection, $srcTable, $targetTable, $fields)
    {
        $updateField = end($fields);

        foreach ($mappedWebsites as $stagingWebsiteId => $masterWebsiteIds) {
            if (empty($stagingWebsiteId) || empty($masterWebsiteIds)) {
                continue;
            }
            $stagingWebsiteId = intval($stagingWebsiteId);

            foreach ($masterWebsiteIds as $masterWebsiteId) {
                if (empty($masterWebsiteId)) {
                    continue;
                }
                $masterWebsiteId = intval($masterWebsiteId);

                $destInsertSql = "UPDATE `{$targetTable}` SET `website_ids` = IF(FIND_IN_SET({$masterWebsiteId},`website_ids`), `website_ids`, CONCAT(`website_ids`,',{$masterWebsiteId}'))
                    WHERE FIND_IN_SET({$stagingWebsiteId},`website_ids`)";

                $connection->query($destInsertSql);
            }
        }

        return $this;
    }

    /**
     * Process Store scope
     *
     * @param string $model
     * @param string $srcTable
     * @param string $targetTable
     * @param mixed  $fields
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    protected function _mergeTableDataInStoreScope($model, $srcTable, $targetTable, $fields)
    {
        $staging    = $this->getStaging();
        $connection = $this->getConnection($model);
        $storesMap  = $staging->getMapperInstance()->getStores();

        if (!empty($storesMap)) {
            foreach ($storesMap as $stagingStoreId => $masterStoreIds) {
                $stagingStoreId = intval($stagingStoreId);

                foreach ($masterStoreIds as $masterStoreId) {
                    $masterStoreId = intval($masterStoreId);

                    $tableDestDesc = $this->getTableProperties($targetTable, $model, true);

                    $_updateField = end($fields);
                    $destInsertSql = "INSERT INTO `{$targetTable}` (".$this->_prepareFields($fields).") (%s) ON DUPLICATE KEY UPDATE `{$_updateField}`=VALUES(`{$_updateField}`)";
                    $_storeFieldNameSql = 'store_id';
                    $_fields = $fields;
                    foreach ($fields as $id => $field) {
                        if ($field == 'store_id') {
                            $_fields[$id] = $masterStoreId;
                        } elseif ($field == 'scope_id') {
                            $_fields[$id] = $masterStoreId;
                            $_storeFieldNameSql = "`scope` = 'stores' AND `{$field}`";
                        }
                    }
                    $srcSelectSql = $this->_getSimpleSelect($_fields, $srcTable, "{$_storeFieldNameSql} = {$stagingStoreId}");
                    $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

                    $connection->query($destInsertSql);
                }
            }
        }

        return $this;
    }






    /**
     * Staging Rollback (Staging Item handle part)
     *
     * @param   object Enterprise_Staging_Model_Staging $staging
     * @param   object Enterprise_Staging_Model_Staging_Event $event
     *
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    public function rollback(Enterprise_Staging_Model_Staging $staging, $event = null)
    {
        parent::rollback($staging, $event);
        $this->_processItemMethodCallback('_rollbackItem');
        return $this;
    }

    /**
     * Prepare table data to rollback
     *
     * @param string  $model
     * @param string  $table
     * @param string  $srcTable
     * @param string  $targetTable
     *
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    protected function _rollbackItem($model, $table, $srcTable, $targetTable)
    {
        $staging    = $this->getStaging();
        $connection = $this->getConnection($model);

        $srcTableDesc = $this->getTableProperties($srcTable, $model);
        if (!$srcTableDesc) {
            return $this;
        }

        $fields = $srcTableDesc['fields'];
        $fields = array_keys($fields);

        $internalPrefix = $this->getEvent()->getId();
        $backupPrefix   = $this->getBackupTablePrefix($internalPrefix);
        $backupTable    = $this->getStagingTableName($model, $targetTable, $backupPrefix, true);

        if ($this->tableExists($model, $backupTable)) {
            if ($this->allowToProceedInWebsiteScope($srcTable, $fields)) {
                $this->_rollbackTableDataInWebsiteScope($model, $backupTable, $targetTable, $connection, $fields);
            }

            if ($this->allowToProceedInStoreScope($srcTable, $fields)) {
                $this->_rollbackTableDataInStoreScope($model, $backupTable, $targetTable, $connection, $fields);
            }
        }

        $this->_proceedTables[$backupTable] = $targetTable;

        return $this;
    }

    /**
     * process website rollback
     *
     * @param string $model
     * @param string $srcTable
     * @param string $targetTable
     * @param $connection
     * @param mixed $fields
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    protected function _rollbackTableDataInWebsiteScope($model, $srcTable, $targetTable, $connection, $fields)
    {
        $staging        = $this->getStaging();
        $mergedWebsites = $staging->getMapperInstance()->getWebsites();

        $_updateField = end($fields);

        if (!empty($mergedWebsites)) {
            foreach ($mergedWebsites as $stagingWebsiteId => $masterWebsiteIds) {
                if (!empty($masterWebsiteIds)) {
                    $_websiteFieldNameSql = 'website_id';
                    if (in_array('website_id', $fields)) {
                        $_websiteFieldNameSql = " `{$srcTable}`.`website_id` IN (" . implode(", ", $masterWebsiteIds). ")";
                    } elseif (in_array('scope_id', $fields)) {
                        $_websiteFieldNameSql = "`{$srcTable}`.`scope` = 'websites' AND `{$srcTable}`.`scope_id` IN (" . implode(", ", $masterWebsiteIds). ")";
                    } elseif (in_array('website_ids', $fields)) {
                        $whereFields = array();
                        foreach($masterWebsiteIds AS $webId) {
                            $whereFields[] = "FIND_IN_SET($webId, `{$srcTable}`.`website_ids`)";
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
                    $destInsertSql = "INSERT INTO `{$targetTable}` (".$this->_prepareFields($fields).") (%s) ON DUPLICATE KEY UPDATE `{$_updateField}`=VALUES(`{$_updateField}`)";

                    $srcSelectSql = $this->_getSimpleSelect($fields, $srcTable, $_websiteFieldNameSql);
                    $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

                    $connection->query($destInsertSql);
                }
            }
        }

        return $this;
    }

    /**
     * process store scope rollback
     *
     * @param string $model
     * @param string $srcTable
     * @param string $targetTable
     * @param $connection
     * @param mixed $fields
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    protected function _rollbackTableDataInStoreScope($model, $srcTable, $targetTable, $connection, $fields)
    {
        $staging        = $this->getStaging();
        $mergedStores   = $staging->getMapperInstance()->getStores();

        if (!empty($mergedStores)) {
            foreach ($mergedStores as $stagingStoreId => $masterStoreIds) {
                if (empty($stagingStoreId) || empty($masterStoreIds)) {
                    continue;
                }
                $stagingStoreId = intval($stagingStoreId);

                foreach ($masterStoreIds as $masterStoreId) {
                    if (empty($masterStoreId)) {
                        continue;
                    }
                    $masterStoreId = intval($masterStoreId);

                    $tableDestDesc = $this->getTableProperties($targetTable, $model);
                    if (!$tableDestDesc) {
                        continue;
                    }

                    $_updateField = end($fields);

                    $_storeFieldNameSql = "`{$srcTable}`.`store_id`";
                    $_fields = $fields;

                    foreach ($_fields as $id => $field) {
                        if ($field == 'store_id') {
                            $_fields[$id] = $masterStoreId;
                        } elseif ($field == 'scope_id') {
                            $_storeFieldNameSql = "`{$srcTable}`.`scope` = 'stores' AND `{$srcTable}`.`{$field}`";
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
                    $destInsertSql = "INSERT INTO `{$targetTable}` (".$this->_prepareFields($fields).") (%s) ON DUPLICATE KEY UPDATE `{$_updateField}`=VALUES(`{$_updateField}`)";

                    $srcSelectSql = $this->_getSimpleSelect($_fields, $srcTable, "{$_storeFieldNameSql} = {$masterStoreId}");
                    $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

                    $connection->query($destInsertSql);
                }
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
    protected function _deleteDataByKeys($type='UNIQUE', $scope='websites', $srcTable, $targetTable, $masterIds, $slaveIds, $keys, $addidtionalWhereCondition=null)
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
                        $_websiteFieldNameSql[] = " T1.`{$field}` $slaveWhere
                            AND T2.`{$field}` $masterWhere ";

                    } elseif ($field == 'scope_id') {

                        $_websiteFieldNameSql[] = " T1.`scope` = '{$scope}' AND T1.`{$field}` $slaveWhere
                            AND T2.`{$field}` $masterWhere ";

                    } else { //website_ids is update data as rule, so it must be in backup.

                        $_websiteFieldNameSql[] = "T1.`$field` = T2.`$field`";

                    }
                }

                $sql = "DELETE T1.* FROM `{$targetTable}` as T1, `{$srcTable}` as T2 WHERE " . implode(" AND " , $_websiteFieldNameSql);
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
     * get Staging Table Name
     *
     * @param string $srcModel
     * @param string $srcTable
     * @param bool $usedStorageMethod
     * @return string
     */
    protected function _getStagingTableName($srcModel, $srcTable, $usedStorageMethod=null)
    {
        if (!$usedStorageMethod) {
            $targetTable = $srcTable;
        } elseif ($usedStorageMethod == Enterprise_Staging_Model_Staging_Config::STORAGE_METHOD_PREFIX) {
            $targetTable = $this->getStagingTableName($srcModel, $srcTable);
        } else {
            // TODO case for staging that use new db
            throw new Enterprise_Staging_Exception('Wrong Storage Method!');
        }

        if (!$this->tableExists('enterprise_staging', $targetTable)) {
            $targetTable = $srcTable;
            //throw new Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Staging Table %s doesn\'t exists',$targetTable));
        }

        return $targetTable;
    }




    protected function _matchTable($table, $code, $model)
    {
        if ($model == 'catalog') {
            if (strpos($table, $code) !== 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Prepares data for action and makes callback
     *
     * @param string $callbackMethod
     *
     * @return Enterprise_Staging_Model_Mysql4_Adapter_Item_Default
     */
    protected function _processItemMethodCallback($callbackMethod)
    {
        $itemXmlConfig      = $this->getConfig();

        $usedStorageMethod  = (string)  $itemXmlConfig->use_storage_method;

        $code   = (string) $itemXmlConfig->getName();
        $model  = (string) $itemXmlConfig->model;
        $tables = (array)  $itemXmlConfig->entities;
        $ignoreTables = (array) $itemXmlConfig->ignore_tables;

        $resourceName = (string) Mage::getConfig()->getNode("global/models/{$model}/resourceModel");
        $entityTables = (array) Mage::getConfig()->getNode("global/models/{$resourceName}/entities");

        foreach ($entityTables as $entityTableConfig) {
            $table = $entityTableConfig->getName();

            if (!empty($tables)) {
                if (!array_key_exists($table, $tables)) {
                    continue;
                }
            }
            if (!empty($ignoreTables)) {
                if (array_key_exists($table, $ignoreTables)) {
                    continue;
                }
            }
            if (!$this->_matchTable($table, $code, $model)) {
                continue;
            }

            $srcTable = $this->getTableName("{$model}/{$table}");
            if (isset($this->_proceedTables[$srcTable])) {
                continue;
            }

            $targetTable = $this->_getStagingTableName($model, $srcTable, $usedStorageMethod);

            if (isset($this->_eavModels["{$model}/{$table}"])) {
                if ($usedStorageMethod === 'table_prefix') {
                    $this->{$callbackMethod}($model, $table, $srcTable, $targetTable);
                }
                foreach ($this->_eavTableTypes as $type) {
                    $_srcTable = $srcTable . '_' . $type;
                    $targetTable = $this->_getStagingTableName($model, $_srcTable, $usedStorageMethod);
                    $this->{$callbackMethod}($model, $table, $_srcTable, $targetTable);
                }
                continue;
            } elseif (isset($this->_flatTables[$table])) {
                if ('category_flat' == $table) {
                    if (!Mage::helper('catalog/category_flat')->isEnabled()) {
                        continue;
                    }
                    $flatModel = Mage::getResourceModel('catalog/category_flat');
                    foreach (Mage::app()->getStores() as $store) {
                        $flatTableName = $flatModel->getMainStoreTable($store->getId());
                        if (!$this->tableExists($model, $flatTableName)) {
                            continue;
                        }
                        $targetTable = $this->_getStagingTableName($model, $flatTableName, $usedStorageMethod);
                        $this->{$callbackMethod}($model, $table, $flatTableName, $targetTable);
                    }
                } else {
                    if (!Mage::helper('catalog/product_flat')->isBuilt()) {
                        continue;
                    }
                    foreach (Mage::app()->getStores() as $store) {
                        $flatModel = Mage::getSingleton('catalog/product_flat_indexer');
                        $flatModel->prepareDataStorage($store->getId());
                        $flatTableName  = $flatModel->getResource()->getFlatTableName($store->getId());
                        if (!$this->tableExists($model, $flatTableName)) {
                            continue;
                        }
                        $targetTable = $this->_getStagingTableName($model, $flatTableName, $usedStorageMethod);
                        $this->{$callbackMethod}($model, $table, $flatTableName, $targetTable);
                    }
                }
                // skip main flat type "prefix" table
                continue;
            }

            $this->{$callbackMethod}($model, $table, $srcTable, $targetTable);
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
            $fields = $this->_prepareFields($fields);
        }

        if (isset($where)) {
            $where = " WHERE " . $where;
        }

        return "SELECT $fields FROM `{$table}` $where";
    }

    /**
     * Add sql quotes to fields and return imploded string
     *
     * @param array $fields
     * @return string
     */
    protected function _prepareFields($fields)
    {
        foreach ($fields as $k => $field) {
            if ($field instanceof Zend_Db_Expr) {
                $fields[$k] = (string) $field;
            } elseif (is_int($field)) {
                $fields[$k] = "{$field}";
            } else {
                $fields[$k] = "`{$field}`";
            }
        }
        return implode(', ', $fields);
    }
}
