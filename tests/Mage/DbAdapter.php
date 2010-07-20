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
 * @category    Mage
 * @package     Mage_Tests
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Magento Database TestCase for PHPUnit
 *
 * @category    Tests
 * @package     Mage_Tests
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_DbAdapter
{
    /**
     * Resource connection adapter
     *
     * @var Varien_Db_Adapter_Pdo_Mysql
     */
    protected $_connection;

    /**
     * Fixture tables data array
     *
     * @var arrat
     */
    protected $_tables = array();

    /**
     * Retrieve database adapter
     *
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function getConnection()
    {
        if (is_null($this->_connection)) {
            $this->_connection = Mage::getSingleton('core/resource')
                ->getConnection('core_setup');
        }
        return $this->_connection;
    }

    /**
     * Begin transaction
     *
     * @return Mage_Dbtest
     */
    public function begin()
    {
        $this->getConnection()->beginTransaction();
        return $this;
    }

    /**
     * Commit transaction
     *
     * @return Mage_Dbtest
     */
    public function commit()
    {
        $this->getConnection()->commit();
        return $this;
    }

    /**
     * Roll Back transaction
     *
     * @return Mage_Dbtest
     */
    public function rollback()
    {
        $this->getConnection()->rollback();
        return $this;
    }

    /**
     * Retrieve table with prefix
     *
     * @param string $table
     * @return string
     */
    protected function _getTableName($table)
    {
        $tablePrefix = Mage::getConfig()->getTablePrefix();
        return $tablePrefix . $table;
    }

    /**
     * Retrieve Path for Fixtures
     *
     * @param string $fixtureFileName
     * @return string
     */
    protected function _getFixturePath($fixtureFileName)
    {
        return BP . DS . 'tests' . DS . 'fixtures' . DS
            . $fixtureFileName . '.xml';
    }

    /**
     * Load fixture from file
     *
     * @param string $fileName
     * @return Mage_DbAdapter
     */
    public function loadFixture($fileName)
    {
        $file = $this->_getFixturePath($fileName);
        if (!file_exists($file)) {
            $message = sprintf('Fixture file %s.xml does not exists', $fileName);
            throw new Exception($message);
        }

        $dbFixture = simplexml_load_file($file);
        foreach ($dbFixture->children() as $node) {
            $attr = $node->attributes();
            if ($node->getName() == 'table' && isset($attr['name'])) {
                $tableName = (string)$attr['name'];
                $columns = array();
                $rows = array();
                foreach ($node as $dataNode) {
                    if ($dataNode->getName() == 'column') {
                        $columns[] = (string)$dataNode;
                    }
                    else if ($dataNode->getName() == 'row') {
                        $rows[] = $dataNode;
                    }
                }

                $this->_tables[$tableName] = array(
                    'columns' => $columns,
                    'rows'    => $rows
                );

                $this->_insertFixtureToTable($tableName);
            }
        }

        return $this;
    }

    /**
     * Insert fixture data to table
     *
     * @param string $tableName
     * @return Mage_DbAdapter
     */
    protected function _insertFixtureToTable($tableName)
    {
        $properties = $this->_tables[$tableName];
        $columns    = $properties['columns'];
        $rowSet     = $properties['rows'];

        $tableName  = $this->_getTableName($tableName);

        $this->getConnection()->truncate($tableName);

        $data = array();
        foreach ($rowSet as $row) {
            $item = array();
            /* @var $node SimpleXMLElement */
            $i = 0;
            foreach ($row->children() as $node) {
                switch ($node->getName()) {
                    case 'value':
                        $value = (string)$node;
                        break;
                    default:
                        $value = null;
                        break;
                }
                $item[$columns[$i++]] = $value;
            }
            $data[] = $item;
        }

        if ($data) {
            $this->getConnection()->insertMultiple($tableName, $data);
        }

        return $this;
    }

    /**
     * Retrieve Table Row data from fixture
     *
     * @param string $tableName
     * @param int $rowId
     * @return Varien_Object
     */
    public function getTableRow($tableName, $rowId)
    {
        $row = new Varien_Object();
        if (!isset($this->_tables[$tableName])) {
            return $row;
        }

        $columns = $this->_tables[$tableName]['columns'];
        $rows    = $this->_tables[$tableName]['rows'];

        if (!isset($rows[$rowId])) {
            return $row;
        }

        /* @var $node SimpleXMLElement */
        $i = 0;
        foreach ($rows[$rowId]->children() as $node) {
            switch ($node->getName()) {
                case 'value':
                    $value = (string)$node;
                    break;
                default:
                    $value = null;
                    break;
            }
            $row->setData($columns[$i++], $value);
        }

        return $row;
    }

    /**
     * Retrieve Table rows objects
     *
     * @param string $tableName
     * @return array of Varien_Object
     */
    public function getTableRows($tableName)
    {
        $rows = array();
        if (isset($this->_tables[$tableName])) {
            $count = count($this->_tables[$tableName]['rows']);
            for($i = 0; $i < $count; $i ++) {
                $rows[] = $this->getTableRow($tableName, $i);
            }
        }

        return $rows;
    }

    /**
     * Generate fixture from database
     *
     * @param string $fileName
     * @param array $tables
     * @return bool
     */
    public function generateFixture($fileName, array $tables)
    {
        $xmlHeader = '<'.'?xml version="1.0" encoding="UTF-8" ?'.'>' . "\n";
        $xml = new Varien_Simplexml_Element($xmlHeader . '<dataset></dataset>');

        foreach ($tables as $table) {
            $tableName = $this->_getTableName($table);
            $describe = $this->getConnection()->describeTable($tableName);
            $tableXml = $xml->addChild('table');
            $tableXml->addAttribute('name', $table);

            foreach (array_keys($describe) as $columnName) {
                $tableXml->addChild('column', $columnName);
            }

            $select = $this->getConnection()->select()
                ->from($tableName);
            $dataSet = $this->getConnection()->fetchAll($select);
            foreach ($dataSet as $row) {
                $xmlRow = $tableXml->addChild('row');
                foreach ($row as $v) {
                    if (is_null($v)) {
                        $xmlRow->addChild('null', '');
                    }
                    else {
                        $xmlRow->addChild('value', $v);
                    }
                }
            }
        }

        $xmlContent = $xmlHeader . $xml->asNiceXml();
        file_put_contents($this->_getFixturePath($fileName), $xmlContent);

        return true;
    }
}
