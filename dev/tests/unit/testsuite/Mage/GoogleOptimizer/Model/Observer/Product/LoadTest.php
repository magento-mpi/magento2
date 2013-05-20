<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_Product_LoadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_codeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var Mage_GoogleOptimizer_Model_Observer_Product_Load
     */
    protected $_model;

    public function setUp()
    {

        $this->_helperMock = $this->getMock('Mage_GoogleOptimizer_Helper_Data', array(), array(), '', false);
        $this->_codeMock = $this->getMock(
            'Mage_GoogleOptimizer_Model_Code', array('getId', 'loadScripts'), array(), '', false
        );
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Mage_GoogleOptimizer_Model_Observer_Product_Load', array(
            'helper' => $this->_helperMock, 'modelCode' => $this->_codeMock
        ));
    }

    public function testAppendToProductGoogleExperimentScriptSuccess()
    {
        $event = $this->getMock('Varien_Event', array('getProduct'), array(), '', false);
        $product = $this->getMock(
            'Mage_Catalog_Model_Product', array('setGoogleExperiment', 'getId', 'getStoreId'), array(), '', false
        );

        $event->expects($this->once())->method('getProduct')->will($this->returnValue($product));

        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $values = array(
            'entity_id' => 3,
            'entity_type' => Mage_GoogleOptimizer_Model_Code::CODE_ENTITY_TYPE_PRODUCT,
            'store_id' => 0
        );

        $this->_codeMock->expects($this->once())->method('loadScripts')
            ->with($values['entity_id'], $values['entity_type'], $values['store_id']);

        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(2));

        $product->expects($this->once())->method('getId')->will($this->returnValue($values['entity_id']));
        $product->expects($this->once())->method('setGoogleExperiment')->with($this->_codeMock);
        $product->expects($this->exactly(2))->method('getStoreId')->will($this->returnValue($values['store_id']));

        $this->_model->appendToProductGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testAppendToProductGoogleExperimentScriptFail()
    {
        $event = $this->getMock('Varien_Event', array('getProduct'), array(), '', false);
        $product = $this->getMock(
            'Mage_Catalog_Model_Product', array('setGoogleExperiment', 'getId', 'getStoreId'), array(), '', false
        );

        $event->expects($this->once())->method('getProduct')->will($this->returnValue($product));

        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(false));

        $this->_codeMock->expects($this->never())->method('loadScripts');

        $this->_codeMock->expects($this->never())->method('getId');

        $product->expects($this->never())->method('getId');
        $product->expects($this->never())->method('setGoogleExperiment');
        $product->expects($this->once())->method('getStoreId');

        $this->_model->appendToProductGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testAppendToProductGoogleExperimentScriptFailSecond()
    {
        $event = $this->getMock('Varien_Event', array('getProduct'), array(), '', false);
        $product = $this->getMock(
            'Mage_Catalog_Model_Product', array('setGoogleExperiment', 'getId', 'getStoreId'), array(), '', false
        );

        $event->expects($this->once())->method('getProduct')->will($this->returnValue($product));

        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $values = array(
            'entity_id' => 3,
            'entity_type' => Mage_GoogleOptimizer_Model_Code::CODE_ENTITY_TYPE_PRODUCT,
            'store_id' => 0
        );

        $this->_codeMock->expects($this->once())->method('loadScripts')
            ->with($values['entity_id'], $values['entity_type'], $values['store_id']);

        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(false));

        $product->expects($this->once())->method('getId')->will($this->returnValue($values['entity_id']));
        $product->expects($this->exactly(2))->method('getStoreId')->will($this->returnValue($values['store_id']));

        $product->expects($this->never())->method('setGoogleExperiment');

        $this->_model->appendToProductGoogleExperimentScript($this->_eventObserverMock);
    }
}