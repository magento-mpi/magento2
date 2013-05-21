<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_Product_DeleteTest extends PHPUnit_Framework_TestCase
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
     * @var Mage_GoogleOptimizer_Model_Observer_Product_Delete
     */
    protected $_model;

    public function setUp()
    {
        $this->_codeMock = $this->getMock(
            'Mage_GoogleOptimizer_Model_Code', array('getId', 'loadByEntityIdAndType', 'delete'), array(), '', false
        );

        $this->_requestMock = $this->getMock(
            'Mage_Core_Controller_Request_Http', array(), array(), '', false
        );
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Mage_GoogleOptimizer_Model_Observer_Product_Delete', array(
            'modelCode' => $this->_codeMock
        ));
    }

    public function testDeleteFromProductGoogleExperimentScriptSuccess()
    {
        $event = $this->getMock('Varien_Event', array('getProduct'), array(), '', false);
        $product = $this->getMock(
            'Mage_Catalog_Model_Product', array('getId', 'getStoreId'), array(), '', false
        );

        $event->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $values = array(
            'entity_id' => 3,
            'entity_type' => Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT,
            'store_id' => 0
        );

        $this->_codeMock->expects($this->once())->method('loadByEntityIdAndType')
            ->with($values['entity_id'], $values['entity_type'], $values['store_id']);

        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(2));

        $product->expects($this->once())->method('getId')->will($this->returnValue($values['entity_id']));
        $product->expects($this->once())->method('getStoreId')->will($this->returnValue($values['store_id']));

        $this->_codeMock->expects($this->once())->method('delete');

        $this->_model->deleteProductGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testDeleteFromProductGoogleExperimentScriptFail()
    {
        $event = $this->getMock('Varien_Event', array('getProduct'), array(), '', false);
        $product = $this->getMock(
            'Mage_Catalog_Model_Product', array('getId', 'getStoreId'), array(), '', false
        );

        $event->expects($this->once())->method('getProduct')->will($this->returnValue($product));
        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $values = array(
            'entity_id' => 3,
            'entity_type' => Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT,
            'store_id' => 0
        );

        $this->_codeMock->expects($this->once())->method('loadByEntityIdAndType')
            ->with($values['entity_id'], $values['entity_type'], $values['store_id']);

        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(0));

        $product->expects($this->once())->method('getId')->will($this->returnValue($values['entity_id']));
        $product->expects($this->once())->method('getStoreId')->will($this->returnValue($values['store_id']));

        $this->_codeMock->expects($this->never())->method('delete');

        $this->_model->deleteProductGoogleExperimentScript($this->_eventObserverMock);
    }
}