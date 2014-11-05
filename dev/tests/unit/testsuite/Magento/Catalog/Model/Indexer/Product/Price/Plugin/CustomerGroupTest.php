<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Price\Plugin;

class CustomerGroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Plugin\CustomerGroup
     */
    protected $_model;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_subjectMock;

    /**
     * @var \Magento\Indexer\Model\IndexerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexerRegistryMock;

    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_subjectMock = $this->getMock(
            '\Magento\Customer\Service\V1\CustomerGroupServiceInterface', array(), array(), '', false
        );

        $indexerMock = $this->getMock(
            'Magento\Indexer\Model\Indexer',
            array('getId', 'invalidate'),
            array(),
            '',
            false
        );
        $indexerMock->expects($this->once())->method('invalidate');
        $this->indexerRegistryMock = $this->getMock('Magento\Indexer\Model\IndexerRegistry', ['get'], [], '', false);
        $this->indexerRegistryMock->expects($this->once())
            ->method('get')
            ->with(\Magento\Catalog\Model\Indexer\Product\Price\Processor::INDEXER_ID)
            ->will($this->returnValue($indexerMock));

        $this->_model = $this->_objectManager->getObject(
            '\Magento\Catalog\Model\Indexer\Product\Price\Plugin\CustomerGroup',
            array('indexerRegistry' => $this->indexerRegistryMock)
        );
    }

    public function testAroundDelete()
    {
        $this->assertEquals('return_value', $this->_model->afterDeleteGroup($this->_subjectMock, 'return_value'));
    }

    public function testAroundCreate()
    {
        $this->assertEquals('return_value', $this->_model->afterCreateGroup($this->_subjectMock, 'return_value'));
    }

    public function testAroundUpdate()
    {
        $this->assertEquals('return_value', $this->_model->afterUpdateGroup($this->_subjectMock, 'return_value'));
    }
}
