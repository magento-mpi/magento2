<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Index_Model_Indexer_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Index_Model_Indexer_Factory
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_indexerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_indexerMock = $this->getMock(
            'Magento_Catalog_Model_Category_Indexer_Flat', array(), array(), '', false
        );
        $this->_model = new Magento_Index_Model_Indexer_Factory($this->_objectManagerMock);
    }

    /**
     * @covers Magento_Index_Model_Indexer_Factory::create
     */
    public function testCreate()
    {
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento_Indexer')
            ->will($this->returnValue($this->_indexerMock));

        $this->assertInstanceOf('Magento_Index_Model_Indexer_Abstract', $this->_model->create('Magento_Indexer'));
    }

    /**
     * @covers Magento_Index_Model_Indexer_Factory::create
     */
    public function testCreateWithNoInstance()
    {
        $this->assertEquals(null, $this->_model->create('Magento_Indexer'));
    }
}
