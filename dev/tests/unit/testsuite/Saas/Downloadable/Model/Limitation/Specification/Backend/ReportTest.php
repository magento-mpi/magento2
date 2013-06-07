<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Downloadable_Model_Limitation_Specification_Backend_ReportTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var Saas_Saas_Model_Limitation_SpecificationInterface
     */
    protected $_modelSpecification;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Mage_Core_Controller_Request_Http', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_modelSpecification = $objectManagerHelper->getObject(
            'Saas_Downloadable_Model_Limitation_Specification_Backend_Report'
        );
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $action
     * @dataProvider dataProviderForIsSatisfiedBy
     */
    public function testIsSatisfiedBy($module, $controller, $action)
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')->will($this->returnValue($module));
        $this->_requestMock->expects($this->any())->method('getControllerName')->will($this->returnValue($controller));
        $this->_requestMock->expects($this->any())->method('getActionName')->will($this->returnValue($action));

        $this->assertTrue($this->_modelSpecification->isSatisfiedBy($this->_requestMock));
    }

    /**
     * @return array
     */
    public function dataProviderForIsSatisfiedBy()
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

    /**
     * @param string $action
     * @dataProvider dataProviderForIsNotSatisfied
     */
    public function testIsNotSatisfied($action)
    {
        $this->_requestMock->expects($this->any())->method('getControllerModule')
            ->will($this->returnValue('Mage_Adminhtml'));
        $this->_requestMock->expects($this->any())->method('getControllerName')
            ->will($this->returnValue('report_product'));
        $this->_requestMock->expects($this->any())->method('getActionName')
            ->will($this->returnValue($action));

        $this->assertFalse($this->_modelSpecification->isSatisfiedBy($this->_requestMock));
    }

    /**
     * @return array
     */
    public function dataProviderForIsNotSatisfied()
    {
        return array(
            array('downloads'),
            array('exportDownloadsCsv'),
            array('exportDownloadsExcel'),
        );
    }
}
