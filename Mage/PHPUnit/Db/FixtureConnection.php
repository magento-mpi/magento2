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
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Local database server with fixtures data.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_FixtureConnection
{
    /**
     * Default value for loadFromTable flag
     *
     * @var bool
     */
    const DEFAULT_LOAD_FROM_TABLE = false;

    /**
     * Tables data taken from texture
     *
     * @var array array('table1' => array(array('field' => value, ...), array('field' => value, ...), ...), 'table2' => ...)
     */
    protected $_tables = array();

    /**
     * Select-queries data taken from texture
     *
     * @var array array('SELECT * FROM ...' => array(array('field' => value, ...), array('field' => value, ...), ...), 'SELECT * FROM ...' => ...)
     */
    protected $_selects = array();

    /**
     * Should we load data from table or from select array.
     *
     * @var bool
     */
    protected $_loadFromTable = self::DEFAULT_LOAD_FROM_TABLE;

    /**
     * Instance of server
     *
     * @var Mage_PHPUnit_Db_FixtureConnection
     */
    static protected $_instance;

    /**
     * Creates and returns instance of the object
     *
     * @return Mage_PHPUnit_Db_FixtureConnection
     */
    static public function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Protected constructor of singleton
     */
    protected function __construct()
    {
    }

    /**
     * Cannot clone singleton
     */
    protected function __clone()
    {
    }

    /**
     * Reset data in singleton
     */
    public function reset()
    {
        $this->_tables = array();
        $this->_selects = array();
        $this->_loadFromTable = self::DEFAULT_LOAD_FROM_TABLE;
    }

    /**
     * Returns if the server should load data from table or query
     *
     * @return bool
     */
    public function getLoadFromTable()
    {
        return $this->_loadFromTable;
    }

    /**
     * Sets flag if the server should load data from table or query
     *
     * @param bool $loadFromTable
     * @return Mage_PHPUnit_Db_FixtureConnection
     */
    public function setLoadFromTable($loadFromTable)
    {
        $this->_loadFromTable = $loadFromTable;
        return $this;
    }

    /**
     * Load fixtures from paths to _tables and _selects array
     *
     * @param array|string $paths
     * @return Mage_PHPUnit_Db_FixtureConnection
     */
    public function loadFixtures($paths)
    {
        if (!is_array($paths)) {
            $paths = array($paths);
        }
        foreach ($paths as $path) {
            if ($path) {
                $fixture = $this->_getFixture($path);
                //load tables data from XML
                if ($fixture->tables) {
                    foreach ($fixture->tables->children() as $tableNode) {
                        if (!isset($this->_tables[$tableNode->getName()])) {
                            $this->_tables[$tableNode->getName()] = array();
                        }
                        if ($tableNode->rows) {
                            foreach ($tableNode->rows->children() as $rowNode) {
                                $row = array();
                                foreach ($rowNode->children() as $fieldNode) {
                                    $row[$fieldNode->getName()] = (string)$fieldNode;
                                }
                                $this->_tables[$tableNode->getName()][] = $row;
                            }
                        }
                    }
                }

                //load selects data from XML
                if ($fixture->selects) {
                    foreach ($fixture->selects->children() as $selectNode) {
                        if ($selectNode->query) {
                            $query = (string)$selectNode->query;
                            $this->_selects[$query] = array();
                            if ($selectNode->rows) {
                                foreach ($selectNode->rows->children() as $rowNode) {
                                    $row = array();
                                    foreach ($rowNode->children() as $fieldNode) {
                                        $row[$fieldNode->getName()] = (string)$fieldNode;
                                    }
                                    $this->_selects[$query][] = $row;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Loads fixture from XML file path
     *
     * @param string $fullpath
     * @throws Exception
     * @return SimpleXMLElement
     */
    protected function _getFixture($fullpath)
    {
        if (!file_exists($fullpath)) {
            throw new Exception('Fixture file does not exists');
        }
        if (!is_readable($fullpath)) {
            throw new Exception('Fixture file does not readable');
        }

        return simplexml_load_file($fullpath);
    }

    /**
     * Runs SELECT query and return the result in array
     *
     * @param string|Zend_Db_Select $sql
     * @return array
     */
    public function select($sql)
    {
        if ($this->getLoadFromTable()) {
            if (!($sql instanceof Zend_Db_Select)) {
                throw new Exception('SQL query must be of Zend_Db_Select to select data from table');
            }
            $from = $sql->getPart(Zend_Db_Select::FROM);
            $keys = array_keys($from);
            $from = $from[$keys[0]]['tableName'];
            return $this->selectFromTable($from);
        }
        if ($sql instanceof Zend_Db_Select) {
            $sql = (string)$sql;
        }
        return isset($this->_selects[$sql]) ? $this->_selects[$sql] : array();
    }

    /**
     * Gets data from only table name
     *
     * @param string $table
     * @return array
     */
    public function selectFromTable($table)
    {
        return isset($this->_tables[$table]) ? $this->_tables[$table] : array();
    }

    /**
     * Helper method to export data to XML from tables, queries.
     *
     * @param Zend_Db_Adapter_Abstract $adapter
     * @param array|string $tables
     * @param array|string $selects
     * @param string $toFile
     * @throws Exception
     */
    public function exportToXml(Zend_Db_Adapter_Abstract $adapter, $tables, $selects, $toFile)
    {
        if (!is_array($tables)) {
            $tables = array($tables);
        }
        if (!is_array($selects)) {
            $selects = array($selects);
        }
        $xml = new SimpleXMLElement('<dataset></dataset>');
        //export tables data
        $xmlTables = $xml->addChild('tables');
        foreach ($tables as $table) {
            $select = $adapter->select()
                ->from($table);

            $res = $adapter->query($select);
            $rowsNode = $xmlTables->addChild($table)
                ->addChild('rows');
            while ($row = $res->fetch()) {
                $rowNode = $rowsNode->addChild('row');
                foreach ($row as $field => $value) {
                    $rowNode->addChild($field, $value);
                }
            }
        }

        //export SELECT queries data
        $xmlSelects = $xml->addChild('selects');
        foreach ($selects as $select) {
            $res = $adapter->query($select);
            $selectNode = $xmlSelects->addChild('select');
            $selectNode->addChild('query', (string)$select);
            $rowsNode = $selectNode->addChild('rows');
            while ($row = $res->fetch()) {
                $rowNode = $rowsNode->addChild('row');
                foreach ($row as $field => $value) {
                    $rowNode->addChild($field, $value);
                }
            }
        }
        if ($xml->asXML($toFile) === false) {
            throw new Exception('Cannot export tables to fixture file');
        }
    }
}
