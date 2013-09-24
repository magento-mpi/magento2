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

namespace Magento\Core\Model\Resource\Db;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Resource\Db\AbstractDb
     */
    protected $_model;

    protected function setUp()
    {
        $resource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Resource');
        $this->_model = $this->getMockForAbstractClass('Magento\Core\Model\Resource\Db\AbstractDb',
            array('resource' => $resource)
        );
    }


    public function testConstruct()
    {
        $resourceProperty = new \ReflectionProperty(get_class($this->_model), '_resources');
        $resourceProperty->setAccessible(true);
        $this->assertInstanceOf('Magento\Core\Model\Resource', $resourceProperty->getValue($this->_model));
    }

    public function testSetMainTable()
    {
        $setMainTableMethod = new \ReflectionMethod($this->_model, '_setMainTable');
        $setMainTableMethod->setAccessible(true);

        $tableName = $this->_model->getTable('core_website');
        $idFieldName = 'website_id';

        $setMainTableMethod->invoke($this->_model, $tableName);
        $this->assertEquals($tableName, $this->_model->getMainTable());

        $setMainTableMethod->invoke($this->_model, $tableName, $idFieldName);
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
