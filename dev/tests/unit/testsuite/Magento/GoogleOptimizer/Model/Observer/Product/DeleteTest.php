<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Model\Observer\Product;

class DeleteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_codeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var \Magento\GoogleOptimizer\Model\Observer\Product\Delete
     */
    protected $_model;

    protected function setUp()
    {
        $entityId = 3;
        $storeId = 0;

        $this->_codeMock = $this->getMock('Magento\GoogleOptimizer\Model\Code', array(), array(), '', false);
        $event = $this->getMock('Magento\Framework\Event', array('getProduct'), array(), '', false);
        $this->_eventObserverMock = $this->getMock('Magento\Framework\Event\Observer', array(), array(), '', false);
        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $product = $this->getMock(
            'Magento\Catalog\Model\Product',
            array('getId', 'getStoreId', '__wakeup'),
            array(),
            '',
            false
        );
        $product->expects($this->once())->method('getId')->will($this->returnValue($entityId));
        $product->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));
        $event->expects($this->once())->method('getProduct')->will($this->returnValue($product));

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject(
            'Magento\GoogleOptimizer\Model\Observer\Product\Delete',
            array('modelCode' => $this->_codeMock)
        );
    }

    public function testDeleteFromProductGoogleExperimentScriptSuccess()
    {
        $entityId = 3;
        $storeId = 0;

        $this->_codeMock->expects(
            $this->once()
        )->method(
            'loadByEntityIdAndType'
        )->with(
            $entityId,
            \Magento\GoogleOptimizer\Model\Code::ENTITY_TYPE_PRODUCT,
            $storeId
        );
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(2));
        $this->_codeMock->expects($this->once())->method('delete');

        $this->_model->deleteProductGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testDeleteFromProductGoogleExperimentScriptFail()
    {
        $entityId = 3;
        $storeId = 0;

        $this->_codeMock->expects(
            $this->once()
        )->method(
            'loadByEntityIdAndType'
        )->with(
            $entityId,
            \Magento\GoogleOptimizer\Model\Code::ENTITY_TYPE_PRODUCT,
            $storeId
        );
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(0));
        $this->_codeMock->expects($this->never())->method('delete');

        $this->_model->deleteProductGoogleExperimentScript($this->_eventObserverMock);
    }
}
