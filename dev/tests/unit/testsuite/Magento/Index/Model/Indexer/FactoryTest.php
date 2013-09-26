<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Index\Model\Indexer;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Index\Model\Indexer\Factory
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_indexerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_indexerMock = $this->getMock(
            'Magento\Catalog\Model\Category\Indexer\Flat', array(), array(), '', false
        );
        $this->_model = new \Magento\Index\Model\Indexer\Factory($this->_objectManagerMock);
    }

    /**
     * @covers \Magento\Index\Model\Indexer\Factory::create
     */
    public function testCreate()
    {
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento_Indexer')
            ->will($this->returnValue($this->_indexerMock));

        $this->assertInstanceOf('Magento\Index\Model\Indexer\AbstractIndexer', $this->_model->create('Magento_Indexer'));
    }

    /**
     * @covers \Magento\Index\Model\Indexer\Factory::create
     */
    public function testCreateWithNoInstance()
    {
        $this->assertEquals(null, $this->_model->create('Magento_Indexer'));
    }
}
