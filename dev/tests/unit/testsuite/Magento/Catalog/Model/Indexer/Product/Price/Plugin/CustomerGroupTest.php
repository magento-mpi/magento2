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
     * @var \Magento\Customer\Api\GroupRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_subjectMock;

    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_subjectMock = $this->getMock(
            '\Magento\Customer\Api\GroupRepositoryInterface', array(), array(), '', false
        );

        $indexerMock = $this->getMock(
            'Magento\Indexer\Model\Indexer',
            array('getId', 'invalidate'),
            array(),
            '',
            false
        );
        $indexerMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $indexerMock->expects($this->once())->method('invalidate');

        $this->_model = $this->_objectManager->getObject(
            '\Magento\Catalog\Model\Indexer\Product\Price\Plugin\CustomerGroup',
            array('indexer' => $indexerMock)
        );
    }

    public function testAroundDelete()
    {
        $this->assertEquals('return_value', $this->_model->afterDelete($this->_subjectMock, 'return_value'));
    }

    public function testAroundDeleteById()
    {
        $this->assertEquals('return_value', $this->_model->afterDeleteById($this->_subjectMock, 'return_value'));
    }

    public function testAroundSave()
    {
        $this->assertEquals('return_value', $this->_model->afterSave($this->_subjectMock, 'return_value'));
    }
}
