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

namespace Magento\Core\Model;

class ResourceMysqlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Resource
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = \Mage::getModel('Magento\Core\Model\Resource');
    }

    public function testGetConnectionTypeInstance()
    {
        $this->assertInstanceOf(
            'Magento\Core\Model\Resource\Type\Db\Pdo\Mysql',
            $this->_model->getConnectionTypeInstance('pdo_mysql')
        );
    }

    public function testResourceTypeDb()
    {
        $resource = $this->_model->getConnectionTypeInstance('pdo_mysql');
        $this->assertEquals('Magento\Core\Model\Resource\Entity\Table', $resource->getEntityClass(), 'Entity class');

        $resource->setName('test');
        $this->assertEquals('test', $resource->getName(), 'Set/Get name');

        $this->assertInstanceOf(
            'Zend_Db_Adapter_Abstract',
            $resource->getConnection(\Mage::getConfig()->
                getNode('global/resources/default_setup/connection')->asArray())
        );

    }

    public function testCreateConnection()
    {
        $this->assertFalse($this->_model->createConnection('test_false', 'test', 'test'));
        $this->assertInstanceOf(
            'Magento\DB\Adapter\Pdo\Mysql',
            $this->_model->createConnection(
                'test',
                'pdo_mysql',
                \Mage::getConfig()->getNode('global/resources/default_setup/connection')->asArray()
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
            $this->_model
                ->getFkName('sales_flat_creditmemo_comment', 'parent_id', 'sales_flat_creditmemo', 'entity_id')
        );
    }
}
