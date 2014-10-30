<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Flat;

class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Processor
     */
    protected $_model;

    /**
     * @var \Magento\Indexer\Model\Indexer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_indexerMock;

    /**
     * @var \Magento\Indexer\Model\IndexerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_indexerFactoryMock;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_stateMock;

    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_indexerMock = $this->getMock(
            'Magento\Indexer\Model\Indexer',
            array('getId', 'invalidate'),
            array(),
            '',
            false
        );
        $this->_indexerMock->expects($this->any())->method('getId')->will($this->returnValue(1));

        $this->_indexerFactoryMock = $this->getMock(
            'Magento\Indexer\Model\IndexerFactory', array('create'), array(), '', false
        );

        $this->_indexerFactoryMock
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_indexerMock));

        $this->_stateMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Flat\State',
            array('isFlatEnabled'),
            array(),
            '',
            false
        );
        $this->_model = $this->_objectManager->getObject('Magento\Catalog\Model\Indexer\Product\Flat\Processor', array(
            'indexerFactory' => $this->_indexerFactoryMock,
            'state'  => $this->_stateMock
        ));
    }

    /**
     * Test get indexer instance
     */
    public function testGetIndexer()
    {
        $this->assertInstanceOf('\Magento\Indexer\Model\Indexer', $this->_model->getIndexer());
    }

    /**
     * Test mark indexer as invalid if enabled
     */
    public function testMarkIndexerAsInvalid()
    {
        $this->markTestIncomplete('MAGETWO-28043');
        $this->_stateMock->expects($this->once())->method('isFlatEnabled')->will($this->returnValue(true));
        $this->_indexerMock->expects($this->once())->method('invalidate');
        $this->_model->markIndexerAsInvalid();
    }

    /**
     * Test mark indexer as invalid if disabled
     */
    public function testMarkDisabledIndexerAsInvalid()
    {
        $this->_stateMock->expects($this->once())->method('isFlatEnabled')->will($this->returnValue(false));
        $this->_indexerMock->expects($this->never())->method('invalidate');
        $this->_model->markIndexerAsInvalid();
    }
}
