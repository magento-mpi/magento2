<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_Product_SaveTest extends PHPUnit_Framework_TestCase
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
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productMock;

    /**
     * @var Mage_GoogleOptimizer_Model_Observer_Product_Save
     */
    protected $_model;

    public function setUp()
    {
        $this->_helperMock = $this->getMock('Mage_GoogleOptimizer_Helper_Data', array(), array(), '', false);
        $this->_codeMock = $this->getMock('Mage_GoogleOptimizer_Model_Code', array(), array(), '', false);
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);
        $event = $this->getMock('Varien_Event', array('getProduct'), array(), '', false);
        $this->_productMock = $this->getMock(
            'Mage_Catalog_Model_Product', array('setGoogleExperiment', 'getId', 'getStoreId'), array(), '', false
        );
        $event->expects($this->once())->method('getProduct')->will($this->returnValue($this->_productMock));
        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Mage_GoogleOptimizer_Model_Observer_Product_Save', array(
            'helper' => $this->_helperMock, 'modelCode' => $this->_codeMock, 'request' => $this->_requestMock
        ));
    }

    public function testSaveProductGoogleExperimentScriptSuccess()
    {
        $entityId = 3;
        $storeId = 0;
        $codeId = 1;

        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));
        $this->_requestMock->expects($this->once())->method('getParam')->with('google_experiment')
            ->will($this->returnValue(array('code_id' => $codeId, 'experiment_script' => 'some string')));
        $this->_codeMock->expects($this->once())->method('load')->with($codeId);
        $this->_codeMock->expects($this->once())->method('addData')->with(array(
            'entity_id' => $entityId,
            'entity_type' => Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT,
            'store_id' => $storeId,
            'experiment_script' => 'some string',
        ));
        $this->_codeMock->expects($this->once())->method('save');
        $this->_productMock->expects($this->exactly(2))->method('getId')->will($this->returnValue($entityId));
        $this->_productMock->expects($this->exactly(2))->method('getStoreId')->will($this->returnValue($storeId));

        $this->_model->saveProductGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testSaveProductGoogleExperimentScriptFail()
    {
        $entityId = 3;
        $storeId = 0;
        $codeId = 0;

        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));
        $this->_requestMock->expects($this->once())->method('getParam')->with('google_experiment')
            ->will($this->returnValue(array('code_id' => $codeId, 'experiment_script' => 'some string')));
        $this->_codeMock->expects($this->never())->method('load');
        $this->_codeMock->expects($this->once())->method('addData')->with(array(
            'entity_id' => $entityId,
            'entity_type' => Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PRODUCT,
            'store_id' => $storeId,
            'experiment_script' => 'some string',
        ));
        $this->_productMock->expects($this->exactly(2))->method('getId')->will($this->returnValue($entityId));
        $this->_productMock->expects($this->exactly(2))->method('getStoreId')->will($this->returnValue($storeId));

        $this->_model->saveProductGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testSaveProductGoogleExperimentScriptFailSecond()
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(false));
        $this->_requestMock->expects($this->never())->method('getParam');
        $this->_codeMock->expects($this->never())->method('load');
        $this->_codeMock->expects($this->never())->method('addData');
        $this->_productMock->expects($this->never())->method('getId');
        $this->_productMock->expects($this->once())->method('getStoreId');

        $this->_model->saveProductGoogleExperimentScript($this->_eventObserverMock);
    }
}
