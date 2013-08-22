<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Model_Observer_Product_DeleteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_codeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var Magento_GoogleOptimizer_Model_Observer_Product_Delete
     */
    protected $_model;

    public function setUp()
    {
        $entityId = 3;
        $storeId = 0;

        $this->_codeMock = $this->getMock('Magento_GoogleOptimizer_Model_Code', array(), array(), '', false);
        $event = $this->getMock('Magento_Event', array('getProduct'), array(), '', false);
        $this->_eventObserverMock = $this->getMock('Magento_Event_Observer', array(), array(), '', false);
        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $product = $this->getMock('Magento_Catalog_Model_Product', array('getId', 'getStoreId'), array(), '', false);
        $product->expects($this->once())->method('getId')->will($this->returnValue($entityId));
        $product->expects($this->once())->method('getStoreId')->will($this->returnValue($storeId));
        $event->expects($this->once())->method('getProduct')->will($this->returnValue($product));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento_GoogleOptimizer_Model_Observer_Product_Delete', array(
            'modelCode' => $this->_codeMock
        ));
    }

    public function testDeleteFromProductGoogleExperimentScriptSuccess()
    {
        $entityId = 3;
        $storeId = 0;

        $this->_codeMock->expects($this->once())->method('loadByEntityIdAndType')
            ->with($entityId, Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT, $storeId);
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(2));
        $this->_codeMock->expects($this->once())->method('delete');

        $this->_model->deleteProductGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testDeleteFromProductGoogleExperimentScriptFail()
    {
        $entityId = 3;
        $storeId = 0;

        $this->_codeMock->expects($this->once())->method('loadByEntityIdAndType')
            ->with($entityId, Magento_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT, $storeId);
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(0));
        $this->_codeMock->expects($this->never())->method('delete');

        $this->_model->deleteProductGoogleExperimentScript($this->_eventObserverMock);
    }
}
