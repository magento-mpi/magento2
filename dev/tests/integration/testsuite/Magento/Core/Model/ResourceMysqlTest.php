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

class Magento_Core_Model_ResourceMysqlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Resource
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getModel('Magento_Core_Model_Resource');
    }

    public function testGetConnectionTypeInstance()
    {
        $this->assertInstanceOf(
            'Magento_Core_Model_Resource_Type_Db_Pdo_Mysql',
            $this->_model->getConnectionTypeInstance('pdo_mysql')
        );
    }

    public function testResourceTypeDb()
    {
        $configModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config');
        $resource = $this->_model->getConnectionTypeInstance('pdo_mysql');
        $this->assertEquals('Magento_Core_Model_Resource_Entity_Table', $resource->getEntityClass(), 'Entity class');

        $resource->setName('test');
        $this->assertEquals('test', $resource->getName(), 'Set/Get name');

        $this->assertInstanceOf(
            'Zend_Db_Adapter_Abstract',
            $resource->getConnection($configModel->getNode('global/resources/default_setup/connection')->asArray())
        );

    }

    public function testCreateConnection()
    {
        $configModel = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Config');
        $this->assertFalse($this->_model->createConnection('test_false', 'test', 'test'));
        $this->assertInstanceOf(
            'Magento_DB_Adapter_Pdo_Mysql',
            $this->_model->createConnection(
                'test',
                'pdo_mysql',
                $configModel->getNode('global/resources/default_setup/connection')->asArray()
            )
        );

    }

    /**
     * @magentoConfigFixture global/resources/db/table_prefix prefix_
     */
    public function testGetIdxName()
    {
        $this->assertEquals(
            'IDX_PREFIX_CORE_STORE_STORE_ID',
            $this->_model->getIdxName('core_store', array('store_id'))
        );
    }

    public function testGetFkName()
    {
        $this->assertStringStartsWith(
            'FK_',
            $this->_model->getFkName('sales_flat_creditmemo_comment', 'parent_id', 'sales_flat_creditmemo', 'entity_id')
        );
    }
}
