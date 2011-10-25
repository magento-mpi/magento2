<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Resource_Db_AbstractTestAbstract extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        return $this;
    }

    public function getResources()
    {
        return $this->_resources;
    }

    public function setMainTable($mainTable, $idFieldName = null)
    {
        return parent::_setMainTable($mainTable, $idFieldName);
    }
}

/**
 * @group module:Mage_Core
 */
class Mage_Core_Model_Resource_Db_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Resource_Db_AbstractTestAbstract
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Core_Model_Resource_Db_AbstractTestAbstract();
    }

    public function testConstruct()
    {
        $model = new Mage_Core_Model_Resource_Db_AbstractTestAbstract();
        $this->assertInstanceOf('Mage_Core_Model_Resource', $model->getResources());
    }

    public function testSetMainTable()
    {
        $tableName = $this->_model->getTable('core_website');
        $idFieldName = 'website_id';

        $this->_model->setMainTable($tableName);
        $this->assertEquals($tableName, $this->_model->getMainTable());

        $this->_model->setMainTable($tableName, $idFieldName);
        $this->assertEquals($tableName, $this->_model->getMainTable());
        $this->assertEquals($idFieldName, $this->_model->getIdFieldName());
    }

    /**
     * @magentoConfigFixture global/resources/db/table_prefix prefix_
     */
    public function testGetTableName()
    {
        $tableNameOrig = 'core_website';
        $tableSuffix = 'suffix';
        $tableName = $this->_model->getTable(array($tableNameOrig, $tableSuffix));
        $this->assertEquals('prefix_core_website_suffix', $tableName);
    }
}
