<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Resource_Db_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Resource_Db_Abstract
     */
    protected $_model;

    protected function setUp()
    {
        $resource = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Resource');
        $this->_model = $this->getMockForAbstractClass('Magento_Core_Model_Resource_Db_Abstract',
            array('resource' => $resource)
        );
    }


    public function testConstruct()
    {
        $resourceProperty = new ReflectionProperty(get_class($this->_model), '_resources');
        $resourceProperty->setAccessible(true);
        $this->assertInstanceOf('Magento_Core_Model_Resource', $resourceProperty->getValue($this->_model));
    }

    public function testSetMainTable()
    {
        $setMainTableMethod = new ReflectionMethod($this->_model, '_setMainTable');
        $setMainTableMethod->setAccessible(true);

        $tableName = $this->_model->getTable('core_website');
        $idFieldName = 'website_id';

        $setMainTableMethod->invoke($this->_model, $tableName);
        $this->assertEquals($tableName, $this->_model->getMainTable());

        $setMainTableMethod->invoke($this->_model, $tableName, $idFieldName);
        $this->assertEquals($tableName, $this->_model->getMainTable());
        $this->assertEquals($idFieldName, $this->_model->getIdFieldName());
    }


    public function testGetTableName()
    {
        $tableNameOrig = 'core_website';
        $tableSuffix = 'suffix';
        $resource = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create(
            'Magento_Core_Model_Resource', array('tablePrefix' => 'prefix_')
        );

        $model = $this->getMockForAbstractClass('Magento_Core_Model_Resource_Db_Abstract',
            array('resource' => $resource)
        );

        $tableName = $model->getTable(array($tableNameOrig, $tableSuffix));
        $this->assertEquals('prefix_core_website_suffix', $tableName);
    }
}
