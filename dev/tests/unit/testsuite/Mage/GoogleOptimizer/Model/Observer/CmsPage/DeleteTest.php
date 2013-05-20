<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_CmsPage_DeleteTest extends PHPUnit_Framework_TestCase
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
     * @var Mage_GoogleOptimizer_Model_Observer_Product_Load
     */
    protected $_model;

    public function setUp()
    {
        $this->_codeMock = $this->getMock(
            'Mage_GoogleOptimizer_Model_Code', array('getId', 'loadScripts', 'delete'), array(), '', false
        );

        $this->_requestMock = $this->getMock(
            'Mage_Core_Controller_Request_Http', array(), array(), '', false
        );
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Mage_GoogleOptimizer_Model_Observer_CmsPage_Delete', array(
            'modelCode' => $this->_codeMock
        ));
    }

    public function testDeleteFromPageGoogleExperimentScriptSuccess()
    {
        $event = $this->getMock('Varien_Event', array('getObject'), array(), '', false);
        $page = $this->getMock(
            'Mage_Cms_Model_Page', array('getId', 'getStoreId'), array(), '', false
        );

        $event->expects($this->once())->method('getObject')->will($this->returnValue($page));
        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $values = array(
            'entity_id' => 3,
            'entity_type' => Mage_GoogleOptimizer_Model_Code::CODE_ENTITY_TYPE_CMS,
            'store_id' => 0
        );

        $this->_codeMock->expects($this->once())->method('loadScripts')
            ->with($values['entity_id'], $values['entity_type'], $values['store_id']);

        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(2));

        $page->expects($this->once())->method('getId')->will($this->returnValue($values['entity_id']));

        $this->_codeMock->expects($this->once())->method('delete');

        $this->_model->deleteCmsGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testDeleteFromPageGoogleExperimentScriptFail()
    {
        $event = $this->getMock('Varien_Event', array('getObject'), array(), '', false);
        $page = $this->getMock(
            'Mage_Cms_Model_Page', array('getId', 'getStoreId'), array(), '', false
        );

        $event->expects($this->once())->method('getObject')->will($this->returnValue($page));
        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $values = array(
            'entity_id' => 3,
            'entity_type' => Mage_GoogleOptimizer_Model_Code::CODE_ENTITY_TYPE_CMS,
            'store_id' => 0
        );

        $this->_codeMock->expects($this->once())->method('loadScripts')
            ->with($values['entity_id'], $values['entity_type'], $values['store_id']);

        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(0));

        $page->expects($this->once())->method('getId')->will($this->returnValue($values['entity_id']));

        $this->_codeMock->expects($this->never())->method('delete');

        $this->_model->deleteCmsGoogleExperimentScript($this->_eventObserverMock);
    }
}