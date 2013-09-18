<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_GoogleOptimizer_Model_Observer_CmsPage_DeleteTest extends PHPUnit_Framework_TestCase
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
     * @var \Magento\GoogleOptimizer\Model\Observer\CmsPage\Delete
     */
    protected $_model;

    protected function setUp()
    {
        $this->_codeMock = $this->getMock('Magento\GoogleOptimizer\Model\Code', array(), array(), '', false);
        $this->_requestMock = $this->getMock('Magento\Core\Controller\Request\Http', array(), array(), '', false);

        $page = $this->getMock('Magento\Cms\Model\Page', array(), array(), '', false);
        $page->expects($this->once())->method('getId')->will($this->returnValue(3));
        $event = $this->getMock('Magento\Event', array('getObject'), array(), '', false);
        $event->expects($this->once())->method('getObject')->will($this->returnValue($page));
        $this->_eventObserverMock = $this->getMock('Magento\Event\Observer', array(), array(), '', false);
        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento\GoogleOptimizer\Model\Observer\CmsPage\Delete', array(
            'modelCode' => $this->_codeMock
        ));
    }

    public function testDeleteFromPageGoogleExperimentScriptSuccess()
    {
        $entityId = 3;
        $storeId = 0;

        $this->_codeMock->expects($this->once())->method('loadByEntityIdAndType')
            ->with($entityId, \Magento\GoogleOptimizer\Model\Code::ENTITY_TYPE_PAGE, $storeId);
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(2));
        $this->_codeMock->expects($this->once())->method('delete');

        $this->_model->deleteCmsGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testDeleteFromPageGoogleExperimentScriptFail()
    {
        $entityId = 3;
        $storeId = 0;

        $this->_codeMock->expects($this->once())->method('loadByEntityIdAndType')
            ->with($entityId, \Magento\GoogleOptimizer\Model\Code::ENTITY_TYPE_PAGE, $storeId);
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(0));
        $this->_codeMock->expects($this->never())->method('delete');

        $this->_model->deleteCmsGoogleExperimentScript($this->_eventObserverMock);
    }
}
