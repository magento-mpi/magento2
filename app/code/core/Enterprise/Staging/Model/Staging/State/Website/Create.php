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

class Enterprise_Staging_Model_Staging_State_Website_Create extends Enterprise_Staging_Model_Staging_State_Website_Abstract
{
    protected $_proceedTables = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        $this->getAdapter()->beginTransaction('enterprise_staging');
        try {
            $this->_processStaging();
            $this->getAdapter()->commitTransaction('enterprise_staging');
        } catch (Zend_Db_Statement_Exception $e) {
            $this->getAdapter()->rollbackTransaction('enterprise_staging');
            throw new Enterprise_Staging_Exception($e);
        } catch (Exception $e) {
            $this->getAdapter()->rollbackTransaction('enterprise_staging');
            throw new Enterprise_Staging_Exception($e);
        }
        return $this;
    }

    protected function _processStaging($staging = null)
    {
        if (is_null($staging)) {
            $staging = $this->getStaging();
        }

        $websites = $staging->getWebsitesCollection();
        foreach ($websites as $website) {
            $this->setStagingWebsite($website);
            $this->_processWebsiteData($website);

            $this->_processStoresData($website);
        }
    }

    protected function _processStoresData($website = null)
    {
        if (is_null($website)) {
            $website = $this->getWebsite();
        }

        $stagingItems = Enterprise_Staging_Model_Staging_Config::getStagingItems();

        $stagingStores    = $website->getStoresCollection();

        foreach ($stagingStores as $stagingStore) {
            $usedItems = $this->getStaging()->getMapperInstance()
                ->getStoreUsedCreateItems($website->getMasterWebsiteId(), $stagingStore->getMasterStoreId());

            foreach ($usedItems as $usedItem) {
                $item = $stagingItems->{$usedItem['code']};
                if (!$item->code) {
                    continue;
                }
                if ((int)$item->is_backend) {
                    continue;
                }

                $internalMode = !(int)$item->use_table_prefix;

                $tables = (array) $item->entities;
                $this->_processData($stagingStore, (string)$item->model, $tables, $internalMode);
            }
        }


        return $this;
    }

    protected function _processWebsiteData($website = null)
    {
        if (is_null($website)) {
            $website = $this->getWebsite();
        }

        $stagingItems = Enterprise_Staging_Model_Staging_Config::getStagingItems();

        $usedItems = $this->getStaging()->getMapperInstance()->getWebsiteUsedCreateItems($website->getMasterWebsiteId());

        foreach ($usedItems as $usedItem) {
            $item = $stagingItems->{$usedItem['code']};
            if (!$item->code) {
                continue;
            }
            if ((int)$item->is_backend) {
                continue;
            }

            $internalMode = !(int)$item->use_table_prefix;

            $tables = (array) $item->entities;
            $this->_processData($website, (string)$item->model, $tables, $internalMode);
        }
        return $this;
    }

    protected function _processData($object, $model, $tables = array(), $internalMode = true)
    {
        if (empty($tables)) {
            $resourceName = (string) Mage::getConfig()->getNode("global/models/{$model}/resourceModel");
            $tables = (array) Mage::getConfig()->getNode("global/models/{$resourceName}/entities");
        }

        foreach ($tables as $table) {
            $table = (array) $table;
            $table = (string) $table['table'];
            if (isset($this->_proceedTables[$table])) {
                continue;
            }

            if (isset($this->_tableModels[$table])) {
                foreach ($this->_eavTableTypes as $type) {
                    $_table = $table . '_' . $type;
                    $this->_processTableData($object, $model, $_table, $internalMode);
                }
                continue;
            }
            if (isset($this->_ignoreTables[$table])) {
                continue;
            }
            $this->_processTableData($object, $model, $table, $internalMode);
        }

        return $this;
    }

    protected function _processTableData($object, $srcModel, $srcTable, $internalMode = true)
    {
        $adapter = $this->getAdapter();

        if ($internalMode) {
            $targetTable = $srcTable;
        } else {
            $targetTable = $this->_getStagingTableName($srcModel, $srcTable);
        }

        $tableSrcDesc = $adapter->getTableProperties($srcModel, $srcTable);
        if (!$tableSrcDesc) {
            throw Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Staging Table %s doesn\'t exists',$srcTable));
        }

        $fields = $tableSrcDesc['fields'];
        foreach ($fields as $id => $field) {
            if ((strpos($srcTable, 'catalog_product_website') === false)
            && (strpos($srcTable, 'catalog_product_enabled_index') === false)
            && (strpos($srcTable, 'catalog_category_product_index') === false))
            if ($field['key'] == 'PRI') {
                unset($fields[$id]);
            }
        }
        $fields = array_keys($fields);

        if($object instanceof Enterprise_Staging_Model_Staging_Website) {
            $this->_processTableDataInWebsiteScope($object, $srcTable, $targetTable, $fields);
        } elseif($object instanceof Enterprise_Staging_Model_Staging_Store) {
            $this->_processTableDataInStoreScope($object, $srcTable, $targetTable, $fields);
        }

        $this->_proceedTables[$srcTable] = true;

        return $this;
    }

    protected function _processTableDataInWebsiteScope($website, $srcTable, $targetTable, $fields)
    {
        if (!in_array('website_id', $fields) && !in_array('website_ids', $fields) && !in_array('scope_id', $fields)) {
            return $this;
        }

        $adapter        = $this->getAdapter();
        $targetModel    = 'enterprise_staging';
        $connection     = $adapter->getConnection($targetModel);

        $mapper         = $this->getStaging()->getMapperInstance();

        $masterWebsite  = $website->getMasterWebsite();

        $masterWebsiteId = $masterWebsite->getId();

        $slaveWebsiteId = $website->getSlaveWebsiteId();
        if (!$slaveWebsiteId) {
            return $this;
        }

        $tableDestDesc = $this->getAdapter()->getTableProperties($targetModel, $targetTable);

        if (!$tableDestDesc) {
            throw Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Staging Table %s doesn\'t exists',$targetTable));
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
                $_websiteFieldNameSql = "scope = 'website' AND {$field} = {$masterWebsiteId}";
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

        return $this;
    }

    protected function _processTableDataInStoreScope($store, $srcTable, $targetTable, $fields)
    {
        if (!in_array('store_id', $fields) && !in_array('scope_id', $fields)) {
            return $this;
        }

        $adapter        = $this->getAdapter();
        $targetModel    = 'enterprise_staging';
        $connection     = $adapter->getConnection($targetModel);

        $mapper         = $this->getStaging()->getMapperInstance();

        $slaveStoreId   = $store->getSlaveStoreId();

        $masterStoreId  = $store->getMasterStoreId();

        if (!$slaveStoreId || !$masterStoreId) {
            return $this;
        }

        $tableDestDesc = $this->getAdapter()->getTableProperties($targetModel, $targetTable);
        if (!$tableDestDesc) {
            throw Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Staging Table %s doesn\'t exists',$targetTable));
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
                $_storeFieldNameSql = "scope = 'store' AND {$field} = {$masterStoreId}";
            }
        }

        $srcSelectSql = "SELECT ".implode(',',$fields)." FROM `{$targetTable}` WHERE {$_storeFieldNameSql}";

        $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
        //echo $destInsertSql.'<br /><br /><br /><br />';
        $connection->query($destInsertSql);

        return $this;
    }
}