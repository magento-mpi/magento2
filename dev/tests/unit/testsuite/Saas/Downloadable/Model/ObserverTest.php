<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Design_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_saasHelperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var Saas_Downloadable_Model_Observer
     */
    protected $_modelObserver;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http');
        $this->_saasHelperMock = $this->getMock('Saas_Saas_Helper_Data', array(), array(), '', false);
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer');

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelObserver = $objectManagerHelper->getObject('Saas_Downloadable_Model_Observer', array(
            'request' => $this->_requestMock,
            'saasHelper' => $this->_saasHelperMock,
        ));
    }

    /**
     * @param string $action
     * @dataProvider dataProviderForDisableReportProductDownloads
     */
    public function testDisableReportProductDownloads($action)
    {
        $this->_requestMock->expects($this->any())->method('getControllerName')
            ->will($this->returnValue('report_product'));
        $this->_requestMock->expects($this->any())->method('getControllerModule')
            ->will($this->returnValue('Mage_Adminhtml'));
        $this->_requestMock->expects($this->any())->method('getActionName')
            ->will($this->returnValue($action));
        $this->_saasHelperMock->expects($this->once())->method('customizeNoRoutForward');

        $this->_modelObserver->disableReportProductDownloads($this->_eventObserverMock);
    }

    /**
     * @return array
     */
    public function dataProviderForDisableReportProductDownloads()
    {
        return array(
            array('downloads'),
            array('exportDownloadsCsv'),
            array('exportDownloadsExcel'),
        );
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $action
     * @dataProvider dataProviderForNonDisableReportProductDownloads
     */
    public function testNonDisableReportProductDownloads($module, $controller, $action)
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')->will($this->returnValue($module));
        $this->_requestMock->expects($this->any())->method('getControllerName')->will($this->returnValue($controller));
        $this->_requestMock->expects($this->any())->method('getActionName')->will($this->returnValue($action));
        $this->_saasHelperMock->expects($this->never())->method('customizeNoRoutForward');

        $this->_modelObserver->disableReportProductDownloads($this->_eventObserverMock);
    }

    /**
     * @return array
     */
    public function dataProviderForNonDisableReportProductDownloads()
    {
        return array(
            array('Mage_Adminhtml', 'unknown', 'unknown'),
            array('Mage_Adminhtml', 'report_product', 'unknown'),
            array('Mage_Adminhtml', 'unknown', 'downloads'),
            array('unknown', 'report_product', 'unknown'),
            array('unknown', 'report_product', 'downloads'),
            array('unknown', 'unknown', 'downloads'),
            array('unknown', 'unknown', 'unknown'),
        );
    }
}
