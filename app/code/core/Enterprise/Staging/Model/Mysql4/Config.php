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

/**
 * Enterprise Staging Config class.
 *
 * Typical procedure is next:
 *
 */
class Enterprise_Staging_Model_Mysql4_Config extends Varien_Object
{
    /**
     * Bypass Database read / write connection
     *
     * @var resource
     */
    protected $_bypass;

    /**
     * Database read / write connections
     *
     * @var resource
     */
    protected $_connections = array();

    /**
     * Database read / write connections
     *
     * @var resource
     */
    protected $_tables = array();

	/**
	 * Staging module config data
	 */
	protected $_config;

	protected $_fromResource = null;

    protected $_toResource = null;

    protected $_resource;

	protected $_checked;

	protected $_prefix;

	protected $_eavTypeModels = array(
	   'catalog' => array(),
	   'customer' => array(),
	   'sales' => array()
    );

    protected $_eavTypeTables = array(
        'catalog_category_entity',
        'catalog_product_entity',
        'customer_entity',
        'customer_address_entity'
    );

    protected $_excludeList = array('core_store', 'core_website', 'eav_attribute', 'eav_attribute_set', 'eav_entity_type', 'cms_page', 'cms_block');

	protected $_eavTableTypes = array('int', 'decimal', 'varchar', 'text', 'datetime');

	protected $_tableModels = array(
	   'catalog_product_entity'    => 'catalog',
	   'catalog_category_entity'   => 'catalog',
	   'customer_entity'           => 'customer',
       'customer_address_entity'   => 'customer',
	);

	protected $_storeId = null;

	protected $_staging = null;

	protected $_stagingWebsite = null;

	public function __construct()
	{
		$this->_config = Mage::getConfig()->getNode('global/enterprise/staging');

		$this->_prefix = Mage::app()->getWebsite()->getCode();
	}

    public function checkStage()
    {
    	if ($this->_isChecked == true) {
    		return $this;
    	}

    	$this->checkResource();

        $this->syncStructure();

        $this->_isChecked = true;

        return $this;
    }

    public function getResource($name)
    {
    	return Mage::getResourceSingleton($name);
    }

    public function checkResource()
    {
    	try {
    		$this->_resource = $this->getResource('enterprise_staging/resource');
    	} catch (Exception $e) {
    		echo $e;STOP();
    		throw Enterprise_Staging_Exception('checkResource()' . $e);
    	}

    	return $this;
    }

    public function getStagingItems()
    {
        return $this->_config->staging_items;
    }

    public function isStagingItem($item, $tableName = null)
    {
        if (in_array($tableName, $this->_excludeList)) {
            return false;
        }

        $stagingItems = $this->getStagingItems();

        if (!is_null($tableName)) {
            if (isset($this->_tableModels[$tableName])) {
                $item = $this->_tableModels[$tableName];
            }
        }

        $stagingItem = $stagingItems->{$item};

        if (!(int)$stagingItem->use_table_prefix) {
            return false;
        }

        if (empty($tableName)) {
            return true;
        } else {
            $tables = (array) $stagingItem->entities;
            if (!empty($tables)) {
                if (array_key_exists($tableName, $tables)) {
                    return true;
                }
            } else {
                return true;
            }
        }

        return false;
    }

    public function syncStructure()
    {
        $stagingItems = $this->getStagingItems();
        foreach ($stagingItems->children() as $item) {
           if (!(int)$item->use_table_prefix) {
                continue;
            }
            $tables = (array) $item->entities;
            $this->syncItemTables((string)$item->model, $tables);
        }
        return $this;
    }

    public function syncItemTables($item, $tables = array())
    {
        if (empty($tables)) {
            $resourceName = (string) Mage::getConfig()->getNode("global/models/{$item}/resourceModel");
            $tables = (array) Mage::getConfig()->getNode("global/models/{$resourceName}/entities");
        }

        foreach ($tables as $table) {
            $table = (array) $table;
            $table = (string) $table['table'];
            $this->syncTable($item, $table);
        }

        return $this;
    }

    public function syncTable($item, $srcTable)
    {
        $targetTable = $this->_getStagingTableName($item, $srcTable);

        $this->_syncTable($item, $srcTable, 'enterprise_staging', $targetTable);

        return $this;
    }

    protected function _syncTable($srcModel, $srcTable, $targetModel, $targetTable, $withData = false, $ignoreEav = false)
    {
        $tableSrcDesc = $this->getTableProperties($srcModel, $srcTable);
        $tableDestDesc = $this->getTableProperties($targetModel, $targetTable);

        if ($tableSrcDesc && !$tableDestDesc) {
            $this->_createTable($targetTable, $targetModel, $srcModel, $tableSrcDesc);
        } else {
            //$this->_syncTableStructure($targetTable, $targetModel, $tableSrcDesc);
        }

        if (!$ignoreEav && in_array($srcTable, $this->_eavTypeTables)) {
            foreach ($this->_eavTableTypes as $type) {
                $_srcTable    = $srcTable . '_' . $type;
                $_targetTable = $targetTable . '_' . $type;
                $this->_syncTable($srcModel, $_srcTable, $targetModel, $_targetTable, $withData, true);
            }
        }
        return $this;
    }

    protected function _createTable($targetTable, $targetModel, $srcModel, $srcTableDescription)
    {
        $connection = $this->_getConnection($targetModel);

        $srcTableDescription['table_name'] = $targetTable;

        $sql = $this->_getCreateSql($srcModel, $srcTableDescription);

//        echo '<pre>';
//        echo $sql;
//        echo '</pre>';
//        echo '<br>';

        $connection->query($sql);

        return $this;
    }

    protected function _getCreateSql($srcModel, $srcTableDescription)
    {
        $_sql = "CREATE TABLE IF NOT EXISTS `{$srcTableDescription['table_name']}`\n";

        $rows = array();
        if (!empty($srcTableDescription['fields'])) {
            foreach ($srcTableDescription['fields'] as $field) {
                $rows[] = $this->_getFieldSql($field);
            }
        }

        foreach ($srcTableDescription['keys'] as $key) {
            $rows[] = $this->_getKeySql($key);
        }
        foreach ($srcTableDescription['constraints'] as $key) {
            $rows[] = $this->_getConstraintSql($key, $srcModel);
        }
        $rows = implode(",\n", $rows);
        $_sql .= " ({$rows})";

        if (!empty($srcTableDescription['engine'])) {
            $_sql .= " ENGINE={$srcTableDescription['engine']}";
        }
        if (!empty($srcTableDescription['charset'])) {
            $_sql .= " DEFAULT CHARSET={$srcTableDescription['charset']}";
        }
        if (!empty($srcTableDescription['collate'])) {
            $_sql .= " COLLATE={$srcTableDescription['collate']}";
        }

        return $_sql;
    }

    protected function _getFieldSql($field)
    {
        $_fieldSql = "`{$field['name']}` {$field['type']} {$field['extra']}";

        switch ((boolean) $field['is_null']) {
            case true:
                $_fieldSql .= "";
                break;
            case false:
                $_fieldSql .= " NOT NULL";
                break;
        }

        switch ($field['default']) {
            case null:
                $_fieldSql .= "";
                break;
            default:
                $_fieldSql .= " DEFAULT '{$field['default']}'";
                break;
        }

        return $_fieldSql;
    }

    protected function _getKeySql($key)
    {

        $_keySql = "";
        switch ((string) $key['type']) {
            case 'INDEX':
                $_keySql .= " KEY";
                break;
            default:
                $_keySql .= " {$key['type']} KEY";
                break;
        }

        $_keySql .= " `{$key['name']}`";
        $fields = array();
        foreach ($key['fields'] as $field) {
            $fields[] = "`{$field}`";
        }
        $fields = implode(',',$fields);
        $_keySql .= "($fields)";
        return $_keySql;
    }

    protected function _getConstraintSql($key, $srcModel)
    {
        $targetRefTable = $this->_getStagingTableName($srcModel, $key['ref_table']);

        if ($targetRefTable) {
            $_refTable = "`$targetRefTable`";
        } else {
            $_refTable = "";
            if ($key['ref_db']) {
                $_refTable .= "`{$key['ref_db']}`.";
            }
            $_refTable .= "`{$key['ref_table']}`";
        }

        $onDelete = "";
        if ($key['on_delete']) {
            $onDelete .= "ON DELETE {$key['on_delete']}";
        }

        $onUpdate = "";
        if ($key['on_update']) {
            $onUpdate .= "ON UPDATE {$key['on_update']}";
        }

        $_keySql = " CONSTRAINT `STAGE_{$key['fk_name']}` FOREIGN KEY (`{$key['pri_field']}`) REFERENCES {$_refTable} (`{$key['ref_field']}`) {$onDelete} {$onUpdate}";

        return $_keySql;
    }






    public function setStaging($staging)
    {
        $this->_staging = $staging;
        return $this;
    }

    public function getStaging()
    {
        return $this->_staging;
    }

    public function setStagingWebsite($website)
    {
        $this->_stagingWebsite = $website;
        return $this;
    }

    public function getStagingWebsite()
    {
        return $this->_stagingWebsite;
    }


    public function syncAllData($staging)
    {
        $this->setStaging($staging);

        $websites = $staging->getWebsitesCollection();
        foreach ($websites as $website) {
            $this->setStagingWebsite($website);
            $this->syncData();
        }
    }

    public function syncData()
    {
        $stagingItems = $this->getStagingItems();
        foreach ($stagingItems->children() as $item) {
            if ((int)$item->is_backend) {
                continue;
            }
            $internalMode = !(int)$item->use_table_prefix;

            $tables = (array) $item->entities;
            $this->syncItemTablesData((string)$item->model, $tables, $internalMode);
        }
        return $this;
    }

    public function syncItemTablesData($item, $tables = array(), $internalMode = true)
    {
        if (empty($tables)) {
            $resourceName = (string) Mage::getConfig()->getNode("global/models/{$item}/resourceModel");
            $tables = (array) Mage::getConfig()->getNode("global/models/{$resourceName}/entities");
        }

        foreach ($tables as $table) {
            $table = (array) $table;
            $table = (string) $table['table'];
            if (isset($this->_tableModels[$table])) {
                continue;
            }
            $this->syncTableData($item, $table, $internalMode);
        }

        return $this;
    }

    public function syncTableData($item, $srcTable, $internalMode = true)
    {
        if ($internalMode) {
            $targetTable = $srcTable;
        } else {
            $targetTable = $this->_getStagingTableName($item, $srcTable);
        }

        $this->_syncTableData($item, $srcTable, 'enterprise_staging', $targetTable);

        return $this;
    }

    protected function _syncTableData($srcModel, $srcTable, $targetModel, $targetTable, $withData = false, $ignoreEav = false)
    {
        $connection = $this->_getConnection($srcModel);

        $tableSrcDesc = $this->getTableProperties($srcModel, $srcTable);
        if (!$tableSrcDesc) {
            echo 'No description for '.$srcTable;STOP();
            throw Enterprise_Staging_Exception('_syncTableData()');
        }
        $fields = $tableSrcDesc['fields'];
        $fields = array_keys($fields);

        if (!in_array('store_id', $fields)) {
            return $this;
        }

        foreach ($fields as $id => $field) {
            if ($field == 'entity_id') {
                unset($fields[$id]);
            }
        }
        $mapper = Mage::getResourceSingleton('enterprise_staging/staging_website_mapper');
        $stagingWebsite = $this->getStagingWebsite();

        $website = $stagingWebsite->getMasterWebsite();

        $storeIds = $website->getStoreIds();

        foreach ($storeIds as $storeId) {

            $tableDestDesc = $this->getTableProperties($targetModel, $targetTable);
            if (!$tableDestDesc) {
                echo 'No description for '.$targetTable;STOP();
                throw Enterprise_Staging_Exception('_syncTableData()');
            }
            $connection = $this->_getConnection($targetModel);
            $destInsertSql = "INSERT INTO `{$targetTable}` (".implode(',',$fields).") (%s) ON DUPLICATE KEY UPDATE (entity_id=entity_id)";

            $slaveStoreId = $mapper->getSlaveStoreIdByMasterStoreId($stagingWebsite, $storeId);

            foreach ($fields as $id => $field) {
                if ($field == 'store_id') {
                    $fields[$id] = $storeId;
                }
            }
            $srcSelectSql = "SELECT ".implode(',',$fields)." FROM `{$srcTable}` WHERE store_id = {$slaveStoreId}";

            $destInsertSql = sprintf($destInsertSql, $srcSelectSql);

            var_dump($destInsertSql);
        }

        return $this;
    }












    /**
     * Retrieve table properties as array
     * fields, keys, constraints, engine, charset, create
     *
     * @param string $item
     * @param string $table
     * @return array
     */
    public function getTableProperties($item, $table)
    {
        if (!$this->tableExists($item, $table)) {
            return false;
        }

        $connection = $this->_getConnection($item);

        $tableName = $this->getTable($table, $item);
        $prefix    = $this->_config[$item]['prefix'];
        $tableProp = array(
            'table_name'  => $tableName,
            'fields'      => array(),
            'keys'        => array(),
            'constraints' => array(),
            'engine'      => 'MYISAM',
            'charset'     => 'utf8',
            'collate'     => null,
            'create_sql'  => null
        );

        // collect fields
        $sql = "SHOW FULL COLUMNS FROM `{$tableName}`";
        $result = $connection->fetchAll($sql);

        foreach($result as $row) {
            $tableProp['fields'][$row["Field"]] = array(
                'name'      => $row["Field"],
                'type'      => $row["Type"],
                'collation' => $row["Collation"],
                'is_null'   => strtoupper($row["Null"]) == 'YES' ? true : false,
                'key'       => $row["Key"],
                'default'   => $row["Default"],
                'extra'     => $row["Extra"],
                'privileges'=> $row["Privileges"]
            );
        }

        // create sql
        $sql = "SHOW CREATE TABLE `{$tableName}`";
        $result = $connection->fetchRow($sql);

        $tableProp['create_sql'] = $result["Create Table"];

        // collect keys
        $regExp  = '#(PRIMARY|UNIQUE|FULLTEXT|FOREIGN)?\sKEY (`[^`]+` )?(\([^\)]+\))#';
        $matches = array();
        preg_match_all($regExp, $tableProp['create_sql'], $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            if (isset($match[1]) && $match[1] == 'PRIMARY') {
                $keyName = 'PRIMARY';
            } elseif (isset($match[1]) && $match[1] == 'FOREIGN') {
                continue;
            } else {
                $keyName = substr($match[2], 1, -2);
            }
            $fields = $fieldsMatches = array();
            preg_match_all("#`([^`]+)`#", $match[3], $fieldsMatches, PREG_SET_ORDER);
            foreach ($fieldsMatches as $field) {
                $fields[] = $field[1];
            }

            $tableProp['keys'][strtoupper($keyName)] = array(
                'type'   => !empty($match[1]) ? $match[1] : 'INDEX',
                'name'   => $keyName,
                'fields' => $fields
            );
        }

        // collect CONSTRAINT
        $regExp  = '#,\s+CONSTRAINT `([^`]*)` FOREIGN KEY \(`([^`]*)`\) '
            . 'REFERENCES (`[^`]*\.)?`([^`]*)` \(`([^`]*)`\)'
            . '( ON DELETE (RESTRICT|CASCADE|SET NULL|NO ACTION))?'
            . '( ON UPDATE (RESTRICT|CASCADE|SET NULL|NO ACTION))?#';
        $matches = array();
        preg_match_all($regExp, $tableProp['create_sql'], $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $tableProp['constraints'][strtoupper($match[1])] = array(
                'fk_name'   => strtoupper($match[1]),
                'ref_db'    => isset($match[3]) ? $match[3] : null,
                'pri_table' => $table,
                'pri_field' => $match[2],
                'ref_table' => substr($match[4], strlen($prefix)),
                'ref_field' => $match[5],
                'on_delete' => isset($match[6]) ? $match[7] : '',
                'on_update' => isset($match[8]) ? $match[9] : ''
            );
        }

        // engine
        $regExp = "#(ENGINE|TYPE)="
            . "(MEMORY|HEAP|INNODB|MYISAM|ISAM|BLACKHOLE|BDB|BERKELEYDB|MRG_MYISAM|ARCHIVE|CSV|EXAMPLE)"
            . "#i";
        $match  = array();
        if (preg_match($regExp, $tableProp['create_sql'], $match)) {
            $tableProp['engine'] = strtoupper($match[2]);
        }

        //charset
        $regExp = "#DEFAULT CHARSET=([a-z0-9]+)( COLLATE=([a-z0-9_]+))?#i";
        $match  = array();
        if (preg_match($regExp, $tableProp['create_sql'], $match)) {
            $tableProp['charset'] = strtolower($match[1]);
            if (isset($match[3])) {
                $tableProp['collate'] = $match[3];
            }
        }

        return $tableProp;
    }

    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $storeId = (int) Mage::app()->getRequest()->getParam('store');
            if (!$storeId) {
                $storeId = (int) Mage::app()->getStore()->getId();
            }
            $this->_storeId = $storeId;
        }
        return $this->_storeId;
    }
    public function getCurrentStagingWebsite()
    {
        $storeId = $this->getStoreId();

        if ($storeId) {
            $store = Mage::app()->getStore($storeId);
            $website = $store->getWebsite();
        } else {
            return false;
        }
        if (!$website->getIsStaging()) {
            return false;
        }

        return $website;
    }



    public function getStagingTableName($tableName, $modelEntity = 'enterprise_staging')
    {
        $arr = explode('/', $modelEntity);
        if (isset($arr[1])) {
            list($model, $entity) = $arr;
        } else {
            throw new Enterprise_Staging_Exception('Enterprise_Staging_Model_Mysql4_Config::getStagingTableName()');
        }

        $storeId = Mage::app()->getRequest()->getParam('store');
        if (!$storeId || !is_int($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }
        if ($storeId) {
            $store = Mage::app()->getStore($storeId);
            $website = $store->getWebsite();
        } else {
            return false;
        }
        if (!$website->getIsStaging()) {
            return false;
        }

        $this->checkStage();
        if ((int)$this->_config->staging_items->{$model}->use_table_prefix) {
            return $this->_getStagingTableName($model, $tableName);
        }
        return false;
    }

    protected function _getStagingTableName($item, $tableName)
    {
        if (!$website = $this->getCurrentStagingWebsite()) {
            return false;
        }

        if (!$this->isStagingItem($item, $tableName)) {
            return false;
        }

        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();

        $stagingPrefix = (string) $website->getCode() . '_';

        return $stagingPrefix . $tablePrefix . $tableName;
    }

    /**
     * Set connection
     *
     * @param mixed $connections   string | array
     * @return Enterprise_Staging_Model_Mysql4_Config
     */
    public function setConnection($connections)
    {
        try {
            if (is_string($connections)) {
                $connections = array($connections);
            }

            foreach ($connections as $k=>$v) {
               $this->_connections[$k] = $this->_resource->getConnection($v);
            }

        } catch (Enterprise_Staging_Exception $e) {
            die('Enterprise_Staging_Model_Mysql4_Config::setConnection()');
        }

        return $this;
    }

    /**
     * Get connection by name or type
     *
     * @param   string $connectionName
     * @return  Zend_Db_Adapter_Abstract
     */
    protected function _getConnection($connectionName, $scope = 'read')
    {
        $connectionName = $connectionName . '_' .$scope;

        if (isset($this->_connections[$connectionName])) {
            return $this->_connections[$connectionName];
        }

        try {
            $this->_connections[$connectionName] = Mage::getSingleton('core/resource')->getConnection($connectionName);
        } catch (Enterprise_Staging_Exception $e) {
            die('Enterprise_Staging_Model_Mysql4_Config::_getConnection()');
        }

        return $this->_connections[$connectionName];
    }

    /**
     * Get table name for the entity
     *
     * @param string $entityName
     */
    public function getTable($entityName, $entity)
    {
        /* FIXME getTable() method doesn't needed yet */
        return $entityName;

        if (isset($this->_tables[$entityName])) {
            return $this->_tables[$entityName];
        }
        if (strpos($entityName, '/')) {
            $this->_tables[$entityName] = $this->_resource->getTableName($entityName);
        } elseif (!empty($this->_resourceModel)) {
            $this->_tables[$entityName] = $this->_resource->getTableName(
                $this->_resourceModel.'/'.$entityName);
        } else {
            $this->_tables[$entityName] = $entityName;
        }
        return $this->_tables[$entityName];
    }

    /**
     * Check exists table
     *
     * @param string $table
     * @param string $entity
     * @return bool
     */
    public function tableExists($item, $table)
    {
        $connection = $this->_getConnection($item);
        $sql = $this->_quote("SHOW TABLES LIKE ?", $this->getTable($table, $item));
        $stmt = $connection->query($sql);
        if (!$stmt->fetch()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Simple quote SQL statement
     * supported ? or %[type] sprintf format
     *
     * @param string $statement
     * @param array $bind
     * @return string
     */
    protected function _quote($statement, $bind = array())
    {
        $statement = str_replace('?', '%s', $statement);
        if (!is_array($bind)) {
            $bind = array($bind);
        }
        foreach ($bind as $k => $v) {
            if (is_numeric($v)) {
                $bind[$k] = $v;
            }
            elseif (is_null($v)) {
                $bind[$k] = 'NULL';
            }
            else {
                $bind[$k] = "'" . mysql_escape_string($v) . "'";
            }
        }
        return vsprintf($statement, $bind);
    }

    /**
     * Apply to Database needed settings
     *
     * @param string $type
     * @return Enterprise_Staging_Model_Mysql4_Config
     */
    public function start($type)
    {
        $this->run('/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */', $type);
        $this->run('/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */', $type);
        $this->run('/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */', $type);
        $this->run('/*!40101 SET NAMES utf8 */', $type);
        $this->run('/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */', $type);
        $this->run('/*!40103 SET TIME_ZONE=\'+00:00\' */', $type);
        $this->run('/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */', $type);
        $this->run('/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */', $type);
        $this->run('/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=\'NO_AUTO_VALUE_ON_ZERO\' */', $type);
        $this->run('/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */', $type);

        return $this;
    }

    /**
     * Run SQL query
     *
     * @param string $sql
     * @param string $type
     * @return resource
     */
    public function run($sql, $type)
    {
        $this->_checkConnection();

        if (!$res = @mysql_query($sql, $this->_getConnection($type))) {
            throw new Exception(sprintf("Error #%d: %s on SQL: %s",
                mysql_errno($this->_getConnection($type)),
                mysql_error($this->_getConnection($type)),
                $sql
            ));
        }
        return $res;
    }

    /**
     * Return old settings to database (applied in start method)
     *
     * @param string $type
     * @return Enterprise_Staging_Model_Mysql4_Config
     */
    public function finish($type)
    {
        $this->run('/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */', $type);
        $this->run('/*!40101 SET SQL_MODE=@OLD_SQL_MODE */', $type);
        $this->run('/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */', $type);
        $this->run('/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */', $type);
        $this->run('/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */', $type);
        $this->run('/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */', $type);
        $this->run('/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */', $type);
        $this->run('/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */', $type);

        return $this;
    }

    /**
     * Begin transaction
     *
     * @param string $type
     * @return Enterprise_Staging_Model_Mysql4_Config
     */
    public function beginTransaction($type)
    {
        $this->run('START TRANSACTION', $type);
        return $this;
    }

    /**
     * Commit transaction
     *
     * @param string $type
     * @return Enterprise_Staging_Model_Mysql4_Config
     */
    public function commitTransaction($type)
    {
        $this->run('COMMIT', $type);
        return $this;
    }

    /**
     * Rollback transaction
     *
     * @param string $type
     * @return Enterprise_Staging_Model_Mysql4_Config
     */
    public function rollbackTransaction($type)
    {
        $this->run('ROLLBACK', $type);
        return $this;
    }

    /**
     * Compare core_resource version
     *
     * @return array
     */
    public function compareResource()
    {
        if (!$this->tableExists('core_resource', self::TYPE_CORRUPTED)) {
            throw new Exception(sprintf('%s DB doesn\'t seem to be a valid Magento database', self::TYPE_CORRUPTED));
        }
        if (!$this->tableExists('core_resource', self::TYPE_REFERENCE)) {
            throw new Exception(sprintf('%s DB doesn\'t seem to be a valid Magento database', self::TYPE_REFERENCE));
        }

        $corrupted = $reference = array();

        $sql = "SELECT * FROM `{$this->getTable('core_resource', self::TYPE_CORRUPTED)}`";
        $res = mysql_query($sql, $this->_getConnection(self::TYPE_CORRUPTED));
        while ($row = mysql_fetch_assoc($res)) {
            $corrupted[$row['code']] = $row['version'];
        }

        $sql = "SELECT * FROM `{$this->getTable('core_resource', self::TYPE_REFERENCE)}`";
        $res = mysql_query($sql, $this->_getConnection(self::TYPE_REFERENCE));
        while ($row = mysql_fetch_assoc($res)) {
            $reference[$row['code']] = $row['version'];
        }

        $compare = array();
        foreach ($reference as $k => $v) {
            if (!isset($corrupted[$k])) {
                $compare[] = sprintf('Module "%s" is not installed in source DB', $k);
            } elseif ($corrupted[$k] != $v) {
                $compare[] = sprintf('Module "%s" has wrong version %s in source DB (target DB contains "%s" ver. %s)', $k, $corrupted[$k], $k, $v);
            }
        }

        return $compare;
    }

    public function getTables($type)
    {
        $this->_checkConnection();
        $this->_checkType($type);
        $prefix = $this->_config[$type]['prefix'];

        $tables = array();

        $sql = 'SHOW TABLES';
        $res = mysql_query($sql, $this->_getConnection($type));
        while ($row = mysql_fetch_row($res)) {
            $tableName = substr($row[0], strlen($prefix));
            $tables[$tableName] = $this->getTableProperties($tableName, $type);
        }

        return $tables;
    }

    /**
     * Add constraint
     *
     * @param array $config
     * @param string $type
     * @return Tools_Db_Repair_Mysql4
     */
    public function addConstraint(array $config, $type)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        $required = array('fk_name', 'pri_table', 'pri_field', 'ref_table', 'ref_field', 'on_update', 'on_delete');
        foreach ($required as $field) {
            if (!array_key_exists($field, $config)) {
                throw new Exception(sprintf('Cannot create CONSTRAINT: invalid required config parameter "%s"', $field));
            }
        }

        if ($config['on_delete'] == '' || strtoupper($config['on_delete']) == 'CASCADE'
            || strtoupper($config['on_delete']) == 'RESTRICT') {
            $sql = "DELETE `p`.* FROM `{$this->getTable($config['pri_table'], $type)}` AS `p`"
                . " LEFT JOIN `{$this->getTable($config['ref_table'], $type)}` AS `r`"
                . " ON `p`.`{$config['pri_field']}` = `r`.`{$config['ref_field']}`"
                . " WHERE `p`.`{$config['pri_field']}` IS NULL";
            $this->run($sql, $type);
        }
        elseif (strtoupper($config['on_delete']) == 'SET NULL') {
            $sql = "UPDATE `{$this->getTable($config['pri_table'], $type)}` AS `p`"
                . " LEFT JOIN `{$this->getTable($config['ref_table'], $type)}` AS `r`"
                . " ON `p`.`{$config['pri_field']}` = `r`.`{$config['ref_field']}`"
                . " SET `p`.`{$config['pri_field']}`=NULL"
                . " WHERE `p`.`{$config['pri_field']}` IS NULL";
            $this->run($sql, $type);
        }

        $sql = "ALTER TABLE `{$this->getTable($config['pri_table'], $type)}`"
            . " ADD CONSTRAINT `{$config['fk_name']}`"
            . " FOREIGN KEY (`{$config['pri_field']}`)"
            . " REFERENCES `{$this->getTable($config['ref_table'], $type)}`"
            . "  (`{$config['ref_field']}`)";
        if (!empty($config['on_delete'])) {
            $sql .= ' ON DELETE ' . strtoupper($config['on_delete']);
        }
        if (!empty($config['on_update'])) {
            $sql .= ' ON UPDATE ' . strtoupper($config['on_update']);
        }

        $this->run($sql, $type);

        return $this;
    }

    /**
     * Drop Foreign Key from table
     *
     * @param string $table
     * @param string $foreignKey
     * @param string $type
     */
    public function dropConstraint($table, $foreignKey, $type)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        $sql = "ALTER TABLE `{$table}` DROP FOREIGN KEY `{$foreignKey}`";
        $this->run($sql, $type);

        return $this;
    }

    /**
     * Add column to table
     * @param string $table
     * @param string $column
     * @param array $config
     * @param string $type
     * @param string|false|null $after
     */
    public function addColumn($table, $column, array $config, $type, $after = null)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        if (!$this->tableExists($table, $type)) {
            return $this;
        }

        $required = array('type', 'is_null', 'default');
        foreach ($required as $field) {
            if (!array_key_exists($field, $config)) {
                throw new Exception(sprintf('Cannot create COLUMN: invalid required config parameter "%s"', $field));
            }
        }

        $sql = "ALTER TABLE `{$this->getTable($table, $type)}` ADD COLUMN `{$column}`"
            . " {$config['type']}"
            . ($config['is_null'] ? "" : " NOT NULL")
            . ($config['default'] ? " DEFAULT '{$config['default']}'" : "")
            . (!empty($config['extra']) ? " {$config['extra']}" : "");
        if ($after === false) {
            $sql .= " FIRST";
        } elseif (!is_null($after)) {
            $sql .= " AFTER `{$after}`";
        }

        $this->run($sql, $type);

        return $this;
    }

    /**
     * Add primary|unique|fulltext|index to table
     *
     * @param string $table
     * @param array $config
     * @param string $type
     * @return Tools_Db_Repair_Mysql4
     */
    public function addKey($table, array $config, $type)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        if (!$this->tableExists($table, $type)) {
            return $this;
        }

        $required = array('type', 'name', 'fields');
        foreach ($required as $field) {
            if (!array_key_exists($field, $config)) {
                throw new Exception(sprintf('Cannot create KEY: invalid required config parameter "%s"', $field));
            }
        }

        switch (strtolower($config['type'])) {
            case 'primary':
                $condition = "PRIMARY KEY";
                break;
            case 'unique':
                $condition = "UNIQUE `{$config['name']}`";
                break;
            case 'fulltext':
                $condition = "FULLTEXT `{$config['name']}`";
                break;
            default:
                $condition = "INDEX `{$config['name']}`";
                break;
        }
        if (!is_array($config['fields'])) {
            $config['fields'] = array($config['fields']);
        }

        $sql = "ALTER TABLE `{$this->getTable($table, $type)}` ADD {$condition}"
            . " (`" . join("`,`", $config['fields']) . "`)";
        $this->run($sql, $type);

        return $this;
    }

    /**
     * Change table storage engine
     *
     * @param string $table
     * @param string $engine
     * @param string $type
     * @return Tools_Db_Repair_Mysql4
     */
    public function changeTableEngine($table, $type, $engine)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        $sql = "ALTER TABLE `{$this->getTable($table, $type)}` ENGINE={$engine}";
        $this->run($sql, $type);

        return $this;
    }

    /**
     * Change table storage engine
     *
     * @param string $table
     * @param string $charset
     * @param string $type
     * @return Tools_Db_Repair_Mysql4
     */
    public function changeTableCharset($table, $type, $charset, $collate = null)
    {
        $this->_checkConnection();
        $this->_checkType($type);

        $sql = "ALTER TABLE `{$this->getTable($table, $type)}` DEFAULT CHARACTER SET={$charset}";
        if ($collate) {
            $sql .= " COLLATE {$collate}";
        }
        $this->run($sql, $type);

        return $this;
    }

    /**
     * Retrieve previous key from array by key
     *
     * @param array $array
     * @param mixed $key
     * @return mixed
     */
    public function arrayPrevKey(array $array, $key)
    {
        $prev = false;
        foreach ($array as $k => $v) {
            if ($k == $key) {
                return $prev;
            }
            $prev = $k;
        }
    }

    /**
     * Retrieve next key from array by key
     *
     * @param array $array
     * @param mixed $key
     * @return mixed
     */
    public function arrayNextKey(array $array, $key)
    {
        $next = false;
        foreach ($array as $k => $v) {
            if ($next === true) {
                return $k;
            }
            if ($k == $key) {
                $next = true;
            }
        }
        return false;
    }
}