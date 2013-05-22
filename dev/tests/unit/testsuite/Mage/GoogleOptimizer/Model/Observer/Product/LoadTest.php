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
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productMock;

    /**
     * @var Mage_GoogleOptimizer_Model_Observer_Product_Load
     */
    protected $_model;

    public function setUp()
    {
        $this->_helperMock = $this->getMock('Mage_GoogleOptimizer_Helper_Data', array(), array(), '', false);
        $this->_codeMock = $this->getMock('Mage_GoogleOptimizer_Model_Code', array(), array(), '', false);
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);
        $event = $this->getMock('Varien_Event', array('getProduct'), array(), '', false);
        $this->_productMock = $this->getMock(
            'Mage_Catalog_Model_Product',
            array('setGoogleExperiment', 'getId', 'getStoreId'),
            array(),
            '',
            false
        );
        $event->expects($this->once())->method('getProduct')->will($this->returnValue($this->_productMock));
        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Mage_GoogleOptimizer_Model_Observer_Product_Load', array(
            'helper' => $this->_helperMock, 'modelCode' => $this->_codeMock
        ));
    }

    public function testAppendToProductGoogleExperimentScriptSuccess()
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $entityId = 3;
        $storeId = 0;

        $this->_codeMock->expects($this->once())->method('loadByEntityIdAndType')
            ->with($entityId, Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT, $storeId);
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(2));
        $this->_productMock->expects($this->once())->method('getId')->will($this->returnValue($entityId));
        $this->_productMock->expects($this->once())->method('setGoogleExperiment')->with($this->_codeMock);
        $this->_productMock->expects($this->exactly(2))->method('getStoreId')->will($this->returnValue($storeId));

        $this->_model->appendToProductGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testAppendToProductGoogleExperimentScriptFail()
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(false));
        $this->_codeMock->expects($this->never())->method('loadByEntityIdAndType');
        $this->_codeMock->expects($this->never())->method('getId');
        $this->_productMock->expects($this->never())->method('getId');
        $this->_productMock->expects($this->never())->method('setGoogleExperiment');
        $this->_productMock->expects($this->once())->method('getStoreId');

        $this->_model->appendToProductGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testAppendToProductGoogleExperimentScriptFailSecond()
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $entityId = 3;
        $storeId = 0;

        $this->_codeMock->expects($this->once())->method('loadByEntityIdAndType')
            ->with($entityId, Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT, $storeId);
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(false));
        $this->_productMock->expects($this->once())->method('getId')->will($this->returnValue($entityId));
        $this->_productMock->expects($this->exactly(2))->method('getStoreId')->will($this->returnValue($storeId));
        $this->_productMock->expects($this->never())->method('setGoogleExperiment');

        $this->_model->appendToProductGoogleExperimentScript($this->_eventObserverMock);
    }
}