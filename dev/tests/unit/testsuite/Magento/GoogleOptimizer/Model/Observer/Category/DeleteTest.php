<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Model\Observer\Category;

class DeleteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_codeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_category;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var \Magento\GoogleOptimizer\Model\Observer\Category\Delete
     */
    protected $_model;

    protected function setUp()
    {
        $this->_codeMock = $this->getMock('Magento\GoogleOptimizer\Model\Code', array(), array(), '', false);
        $this->_category = $this->getMock('Magento\Catalog\Model\Category', array(), array(), '', false);
        $event = $this->getMock('Magento\Framework\Event', array('getCategory'), array(), '', false);
        $event->expects($this->once())->method('getCategory')->will($this->returnValue($this->_category));
        $this->_eventObserverMock = $this->getMock('Magento\Framework\Event\Observer', array(), array(), '', false);
        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject(
            'Magento\GoogleOptimizer\Model\Observer\Category\Delete',
            array('modelCode' => $this->_codeMock)
        );
    }

    public function testDeleteFromCategoryGoogleExperimentScriptSuccess()
    {
        $entityId = 3;
        $storeId = 0;

        $this->_category->expects($this->once())->method('getId')->will($this->returnValue($entityId));
        $this->_category->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));

        $this->_codeMock->expects(
            $this->once()
        )->method(
            'loadByEntityIdAndType'
        )->with(
            $entityId,
            \Magento\GoogleOptimizer\Model\Code::ENTITY_TYPE_CATEGORY,
            $storeId
        );
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(2));
        $this->_codeMock->expects($this->once())->method('delete');

        $this->_model->deleteCategoryGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testDeleteFromCategoryGoogleExperimentScriptFail()
    {
        $entityId = 3;
        $storeId = 0;

        $this->_category->expects($this->once())->method('getId')->will($this->returnValue($entityId));
        $this->_category->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));

        $this->_codeMock->expects(
            $this->once()
        )->method(
            'loadByEntityIdAndType'
        )->with(
            $entityId,
            \Magento\GoogleOptimizer\Model\Code::ENTITY_TYPE_CATEGORY,
            $storeId
        );
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(0));
        $this->_codeMock->expects($this->never())->method('delete');

        $this->_model->deleteCategoryGoogleExperimentScript($this->_eventObserverMock);
    }
}
