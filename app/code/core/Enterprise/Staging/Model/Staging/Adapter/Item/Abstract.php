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

    protected $_srcModel;

    protected $_targetModel;

    public function createItem(Mage_Core_Model_Abstract $object, $itemXmlConfig)
    {
        if ((int)$itemXmlConfig->is_backend) {
            return $this;
        }
        $internalMode   = !(int)  $itemXmlConfig->use_table_prefix;
        $tables         = (array) $itemXmlConfig->entities;

        $this->_processCreateItem($object, (string)$itemXmlConfig->model, $tables, $internalMode);

        return $this;
    }

    protected function _processCreateItem($object, $model, $tables = array(), $internalMode = true)
    {
        $resourceName   = (string) Mage::getConfig()->getNode("global/models/{$model}/resourceModel");
        $entityTables   = (array)  Mage::getConfig()->getNode("global/models/{$resourceName}/entities");

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
                    $_table = $realTableName . '_' . $type;
                    $this->_processItemTableData($object, $model, $_table, $internalMode);
                }
                // ignore main EAV entity table
                continue;
            }
            if (isset($this->_ignoreTables[$table])) {
                continue;
            }
            $this->_processItemTableData($object, $model, $realTableName, $internalMode);
        }

        return $this;
    }

    protected function _processItemTableData($object, $srcModel, $srcTable, $internalMode = true)
    {
        if ($internalMode) {
            $targetTable = $srcTable;
        } else {
            $targetTable = $this->_getStagingTableName($srcModel, $srcTable);
        }

        $tableSrcDesc = $this->getTableProperties($srcModel, $srcTable);
        if (!$tableSrcDesc) {
            throw Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Staging Table %s doesn\'t exists',$srcTable));
        }

        $fields = $tableSrcDesc['fields'];
        foreach ($fields as $id => $field) {
            if ((strpos($srcTable, 'catalog_product_website') === false)
            && (strpos($srcTable, 'catalog_product_enabled_index') === false)
            && (strpos($srcTable, 'catalog_category_product_index') === false)
            && (strpos($srcTable, 'checkout_agreement_store') === false)) {
                if ($field['extra'] == 'auto_increment') {
                    unset($fields[$id]);
                }
            }
        }
        $fields = array_keys($fields);

        if($object instanceof Enterprise_Staging_Model_Staging_Website) {
            $this->_processItemTableDataInWebsiteScope($object, $srcTable, $targetTable, $fields);
        } elseif($object instanceof Enterprise_Staging_Model_Staging_Store) {
            $this->_processItemTableDataInStoreScope($object, $srcTable, $targetTable, $fields);
        }

        return $this;
    }

    protected function _processItemTableDataInWebsiteScope($website, $srcTable, $targetTable, $fields)
    {
        if (isset(self::$_proceedWebsiteScopeTables[$srcTable])) {
            return $this;
        }

        if (!in_array('website_id', $fields) && !in_array('website_ids', $fields) && !in_array('scope_id', $fields)) {
            return $this;
        }

        $targetModel    = 'enterprise_staging';
        $connection     = $this->getConnection($targetModel);

        $mapper         = $this->getStaging()->getMapperInstance();

        $masterWebsite  = $website->getMasterWebsite();

        $masterWebsiteId = $masterWebsite->getId();

        $slaveWebsiteId = $website->getSlaveWebsiteId();
        if (!$slaveWebsiteId) {
            return $this;
        }

        $tableDestDesc = $this->getTableProperties($targetModel, $targetTable);

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

        self::$_proceedWebsiteScopeTables[$srcTable] = true;

        return $this;
    }

    protected function _processItemTableDataInStoreScope($store, $srcTable, $targetTable, $fields)
    {
        if (isset(self::$_proceedStoreScopeTables[$srcTable])) {
            return $this;
        }

        if (!in_array('store_id', $fields) && !in_array('scope_id', $fields)) {
            return $this;
        }

        $targetModel    = 'enterprise_staging';
        $connection     = $this->getConnection($targetModel);

        $mapper         = $this->getStaging()->getMapperInstance();

        $slaveStoreId   = $store->getSlaveStoreId();

        $masterStoreId  = $store->getMasterStoreId();

        if (!$slaveStoreId || !$masterStoreId) {
            return $this;
        }

        $tableDestDesc = $this->getTableProperties($targetModel, $targetTable);
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

        self::$_proceedStoreScopeTables[$srcTable] = true;

        return $this;
    }


























    public function mergeItem(Mage_Core_Model_Abstract $object, $itemXmlConfig)
    {
        if ((int)$itemXmlConfig->is_backend) {
            return $this;
        }
        $internalMode   = !(int)  $itemXmlConfig->use_table_prefix;
        $tables         = (array) $itemXmlConfig->entities;

        $this->_processMergeItem($object, (string)$itemXmlConfig->model, $tables, $internalMode);

        return $this;
    }

    protected function _processMergeItem($object, $model, $tables = array(), $internalMode = true)
    {
        $resourceName = (string) Mage::getConfig()->getNode("global/models/{$model}/resourceModel");
        $entityTables = (array) Mage::getConfig()->getNode("global/models/{$resourceName}/entities");

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
                    $_table = $realTableName . '_' . $type;
                    $this->_mergeItemTableData($object, $model, $_table, $internalMode);
                }
                // ignore main EAV entity table
                continue;
            }
            if (isset($this->_ignoreTables[$table])) {
                continue;
            }

            $this->_mergeItemTableData($object, $model, $realTableName, $internalMode);
        }

        return $this;
    }

    protected function _mergeItemTableData($object, $srcModel, $srcTable, $internalMode = true)
    {
        if ($internalMode) {
            $targetTable = $srcTable;
        } else {
            $targetTable = $this->_getStagingTableName($srcModel, $srcTable);
        }

        $tableSrcDesc = $this->getTableProperties($srcModel, $srcTable);
        if (!$tableSrcDesc) {
            throw Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Staging Table %s doesn\'t exists',$srcTable));
        }

        $fields = $tableSrcDesc['fields'];
        foreach ($fields as $id => $field) {
            if ((strpos($srcTable, 'catalog_product_website') === false)
            && (strpos($srcTable, 'catalog_product_enabled_index') === false)
            && (strpos($srcTable, 'catalog_category_product_index') === false)
            && (strpos($srcTable, 'checkout_agreement_store') === false)) {
                if ($field['extra'] == 'auto_increment') {
                    unset($fields[$id]);
                }
            }
        }
        $fields = array_keys($fields);


        $this->_mergeTableDataInWebsiteScope($object, $srcTable, $targetTable, $fields);

        $this->_mergeTableDataInStoreScope($object, $srcTable, $targetTable, $fields);

        return $this;
    }

    protected function _mergeTableDataInWebsiteScope($website, $srcTable, $targetTable, $fields)
    {
        if (!in_array('website_id', $fields) && !in_array('website_ids', $fields) && !in_array('scope_id', $fields)) {
            return $this;
        }

        $targetModel    = 'enterprise_staging';
        $connection     = $this->getConnection($targetModel);

        $mapper         = $this->getStaging()->getMapperInstance();
        /* @var $mapper Enterprise_Staging_Model_Staging_Mapper_Website */
        $masterWebsite  = $website->getMasterWebsite();

        $slaveWebsite   = $website->getSlaveWebsite();

        $masterWebsiteId = $masterWebsite->getId();

        $slaveWebsiteId = $slaveWebsite->getId();

        $mappedWebsites = $mapper->getUsedWebsites($slaveWebsiteId);

        $tableDestDesc = $this->getTableProperties($targetModel, $targetTable);
        if (!$tableDestDesc) {
            throw Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Staging Table %s doesn\'t exists',$targetTable));
        }
        $_updateField = end($fields);

        foreach ($mappedWebsites['master_website'] as $slaveWebsiteId) {
            if (!$slaveWebsiteId) {
                continue;
            }

            $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$_updateField}=VALUES({$_updateField})";

            $_websiteFieldNameSql = 'website_id';
            $_fields = $fields;
            foreach ($_fields as $id => $field) {
                if ($field == 'website_id') {
                    $_fields[$id] = $masterWebsiteId;
                    $_websiteFieldNameSql = "{$field} = {$slaveWebsiteId}";
                } elseif ($field == 'scope_id') {
                    $_fields[$id] = $masterWebsiteId;
                    $_websiteFieldNameSql = "scope = 'website' AND {$field} = {$slaveWebsiteId}";
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

        return $this;
    }

    protected function _mergeTableDataInStoreScope($website, $srcTable, $targetTable, $fields)
    {
        if (!in_array('store_id', $fields) && !in_array('scope_id', $fields)) {
            return $this;
        }

        $targetModel    = 'enterprise_staging';
        $connection     = $this->getConnection($targetModel);

        $mapper         = $this->getStaging()->getMapperInstance();

        $masterWebsite  = $website->getMasterWebsite();

        $masterStoreIds = $masterWebsite->getStoreIds();

        $slaveWebsite   = $website->getSlaveWebsite();

        $slaveStoreIds  = $slaveWebsite->getStoreIds();

        $slaveToMasterStoreIds = $mapper->getSlaveToMasterStoreIds($masterWebsite->getId());

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

                $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE {$_updateField}=VALUES({$_updateField})";

                $_storeFieldNameSql = 'store_id';
                foreach ($fields as $id => $field) {
                    if ($field == 'store_id') {
                        $fields[$id] = $masterStoreId;
                    } elseif ($field == 'scope_id') {
                        $fields[$id] = $masterStoreId;
                        $_storeFieldNameSql = "scope = 'store' AND {$field}";
                    }
                }

                $srcSelectSql = "SELECT ".implode(',',$fields)." FROM `{$srcTable}` WHERE {$_storeFieldNameSql} = {$slaveStoreId}";

                $destInsertSql = sprintf($destInsertSql, $srcSelectSql);
                //echo $destInsertSql.'<br /><br /><br /><br />';
                $connection->query($destInsertSql);
            }
        }

        return $this;
    }
}