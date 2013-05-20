<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_CmsPage_SaveTest extends PHPUnit_Framework_TestCase
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
     * @var Mage_GoogleOptimizer_Model_Observer_Product_Load
     */
    protected $_model;

    public function setUp()
    {
        $this->_helperMock = $this->getMock('Mage_GoogleOptimizer_Helper_Data', array(), array(), '', false);
        $this->_codeMock = $this->getMock(
            'Mage_GoogleOptimizer_Model_Code',
            array('getId', 'loadScripts', 'load', 'addData', 'save'),
            array(), '', false
        );
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);
        $this->_requestMock = $this->getMock(
            'Mage_Core_Controller_Request_Http', array('getParam'), array(), '', false
        );

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Mage_GoogleOptimizer_Model_Observer_CmsPage_Save', array(
            'helper' => $this->_helperMock, 'modelCode' => $this->_codeMock, 'request' => $this->_requestMock
        ));
    }

    public function testSaveProductGoogleExperimentScriptSuccess()
    {
        $event = $this->getMock('Varien_Event', array('getObject'), array(), '', false);
        $page = $this->getMock(
            'Mage_Catalog_Model_Product', array('setGoogleExperiment', 'getId', 'getStoreId'), array(), '', false
        );

        $event->expects($this->once())->method('getObject')->will($this->returnValue($page));

        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $values = array(
            'entity_id' => 3,
            'entity_type' => Mage_GoogleOptimizer_Model_Code::CODE_ENTITY_TYPE_CMS,
            'store_id' => 0,
            'experiment_script' => 'some string',
        );

        $params = array('code_id' => 1, 'experiment_script' => 'some string');

        $this->_requestMock->expects($this->once())->method('getParam')->with('google_experiment')
            ->will($this->returnValue($params));

        $this->_codeMock->expects($this->once())->method('load')->with($params['code_id']);
        $this->_codeMock->expects($this->once())->method('addData')->with($values);
        $this->_codeMock->expects($this->once())->method('save');

        $page->expects($this->exactly(2))->method('getId')->will($this->returnValue($values['entity_id']));

        $this->_model->saveCmsGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testSaveProductGoogleExperimentScriptFail()
    {
        $event = $this->getMock('Varien_Event', array('getObject'), array(), '', false);
        $page = $this->getMock(
            'Mage_Catalog_Model_Product', array('setGoogleExperiment', 'getId', 'getStoreId'), array(), '', false
        );

        $event->expects($this->once())->method('getObject')->will($this->returnValue($page));

        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $values = array(
            'entity_id' => 3,
            'entity_type' => Mage_GoogleOptimizer_Model_Code::CODE_ENTITY_TYPE_CMS,
            'store_id' => 0,
            'experiment_script' => 'some string',
        );

        $params = array('code_id' => 0, 'experiment_script' => 'some string');

        $this->_requestMock->expects($this->once())->method('getParam')->with('google_experiment')
            ->will($this->returnValue($params));

        $this->_codeMock->expects($this->never())->method('load');
        $this->_codeMock->expects($this->once())->method('addData')->with($values);

        $page->expects($this->exactly(2))->method('getId')->will($this->returnValue($values['entity_id']));

        $this->_model->saveCmsGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testSaveProductGoogleExperimentScriptFailSecond()
    {
        $event = $this->getMock('Varien_Event', array('getObject'), array(), '', false);
        $page = $this->getMock(
            'Mage_Catalog_Model_Product', array('setGoogleExperiment', 'getId', 'getStoreId'), array(), '', false
        );

        $event->expects($this->never())->method('getObject');

        $this->_eventObserverMock->expects($this->never())->method('getEvent');

        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(false));

        $this->_requestMock->expects($this->never())->method('getParam');

        $this->_codeMock->expects($this->never())->method('load');
        $this->_codeMock->expects($this->never())->method('addData');

        $page->expects($this->never())->method('getId');

        $this->_model->saveCmsGoogleExperimentScript($this->_eventObserverMock);
    }
}
