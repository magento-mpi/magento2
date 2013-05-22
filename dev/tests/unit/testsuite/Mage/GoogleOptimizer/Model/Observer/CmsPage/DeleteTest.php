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
     * @var Mage_GoogleOptimizer_Model_Observer_CmsPage_Delete
     */
    protected $_model;

    public function setUp()
    {
        $this->_codeMock = $this->getMock('Mage_GoogleOptimizer_Model_Code', array(), array(), '', false);
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);

        $page = $this->getMock('Mage_Cms_Model_Page', array(), array(), '', false);
        $page->expects($this->once())->method('getId')->will($this->returnValue(3));
        $event = $this->getMock('Varien_Event', array('getObject'), array(), '', false);
        $event->expects($this->once())->method('getObject')->will($this->returnValue($page));
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);
        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Mage_GoogleOptimizer_Model_Observer_CmsPage_Delete', array(
            'modelCode' => $this->_codeMock
        ));
    }

    public function testDeleteFromPageGoogleExperimentScriptSuccess()
    {
        $entityId = 3;
        $storeId = 0;

        $this->_codeMock->expects($this->once())->method('loadByEntityIdAndType')
            ->with($entityId, Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE, $storeId);
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(2));
        $this->_codeMock->expects($this->once())->method('delete');

        $this->_model->deleteCmsGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testDeleteFromPageGoogleExperimentScriptFail()
    {
        $entityId = 3;
        $storeId = 0;

        $this->_codeMock->expects($this->once())->method('loadByEntityIdAndType')
            ->with($entityId, Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE, $storeId);
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(0));
        $this->_codeMock->expects($this->never())->method('delete');

        $this->_model->deleteCmsGoogleExperimentScript($this->_eventObserverMock);
    }
}
