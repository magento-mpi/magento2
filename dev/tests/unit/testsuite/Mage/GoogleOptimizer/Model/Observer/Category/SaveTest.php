<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_Category_SaveTest extends PHPUnit_Framework_TestCase
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
    protected $_categoryMock;

    /**
     * @var Mage_GoogleOptimizer_Model_Observer_Category_Save
     */
    protected $_model;

    public function setUp()
    {
        $this->_helperMock = $this->getMock('Mage_GoogleOptimizer_Helper_Data', array(), array(), '', false);
        $this->_codeMock = $this->getMock('Mage_GoogleOptimizer_Model_Code', array(), array(), '', false);
        $event = $this->getMock('Varien_Event', array('getCategory'), array(), '', false);
        $this->_categoryMock = $this->getMock(
            'Mage_Catalog_Model_Category',
            array('setGoogleExperiment', 'getId', 'getStoreId'),
            array(),
            '',
            false
        );
        $event->expects($this->once())->method('getCategory')->will($this->returnValue($this->_categoryMock));
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);
        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Mage_GoogleOptimizer_Model_Observer_Category_Save', array(
            'helper' => $this->_helperMock, 'modelCode' => $this->_codeMock, 'request' => $this->_requestMock
        ));
    }

    public function testSaveCategoryGoogleExperimentScriptSuccess()
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $entityId = 3;
        $storeId = 0;

        $this->_requestMock->expects($this->once())->method('getParam')->with('google_experiment')
            ->will($this->returnValue(array('code_id' => 1, 'experiment_script' => 'some string')));
        $this->_codeMock->expects($this->once())->method('load')->with(1);
        $this->_codeMock->expects($this->once())->method('addData')->with(array(
            'entity_id' => $entityId,
            'entity_type' => Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_CATEGORY,
            'store_id' => $storeId,
            'experiment_script' => 'some string',
        ));
        $this->_codeMock->expects($this->once())->method('save');
        $this->_categoryMock->expects($this->once())->method('getId')->will($this->returnValue($entityId));
        $this->_categoryMock->expects($this->exactly(2))->method('getStoreId')->will($this->returnValue($storeId));

        $this->_model->saveCategoryGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testSaveCategoryGoogleExperimentScriptFail()
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $entityId = 3;
        $storeId = 0;

        $this->_requestMock->expects($this->once())->method('getParam')->with('google_experiment')
            ->will($this->returnValue( array('code_id' => 0, 'experiment_script' => 'some string')));
        $this->_codeMock->expects($this->never())->method('load');
        $this->_codeMock->expects($this->once())->method('addData')->with(array(
            'entity_id' => $entityId,
            'entity_type' => Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_CATEGORY,
            'store_id' => $storeId,
            'experiment_script' => 'some string',
        ));
        $this->_categoryMock->expects($this->once())->method('getId')->will($this->returnValue($entityId));
        $this->_categoryMock->expects($this->exactly(2))->method('getStoreId')->will($this->returnValue($storeId));

        $this->_model->saveCategoryGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testSaveCategoryGoogleExperimentScriptFailSecond()
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(false));

        $this->_requestMock->expects($this->never())->method('getParam');
        $this->_codeMock->expects($this->never())->method('load');
        $this->_codeMock->expects($this->never())->method('addData');
        $this->_categoryMock->expects($this->never())->method('getId');
        $this->_categoryMock->expects($this->once())->method('getStoreId');

        $this->_model->saveCategoryGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testSaveCategoryGoogleExperimentScriptDeleteCode()
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $entityId = 3;
        $codeId = 1;

        $this->_requestMock->expects($this->once())->method('getParam')->with('google_experiment')
            ->will($this->returnValue(array('code_id' => $codeId, 'experiment_script' => '')));
        $this->_codeMock->expects($this->once())->method('load')->with($codeId);
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue($codeId));
        $this->_codeMock->expects($this->never())->method('addData');
        $this->_codeMock->expects($this->never())->method('save');
        $this->_codeMock->expects($this->once())->method('delete');
        $this->_categoryMock->expects($this->never())->method('getId')->will($this->returnValue($entityId));

        $this->_model->saveCategoryGoogleExperimentScript($this->_eventObserverMock);
    }
}
