<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PageCache_Model_RequestProcessor_MaintenanceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_PageCache_Model_RequestProcessor_Maintenance
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_responseMock;

    protected function setUp()
    {
        $this->_requestMock = $this->getMock('Zend_Controller_Request_Http', array(), array(), '', false);
        $this->_responseMock = $this->getMock('Zend_Controller_Response_Http', array(), array(), '', false);
        $this->_configMock = $this->getMock('Saas_Saas_Model_Maintenance_Config', array(), array(), '', false);
        $this->_model = new Saas_PageCache_Model_RequestProcessor_Maintenance($this->_configMock);
    }

    /**
     * @param array $headers
     * @param bool $clearHeader
     *
     * @dataProvider removeMaintenanceHeaderDataProvider
     */
    public function testRemoveMaintenanceHeader(array $headers, $clearHeader)
    {
        $content = false;
        $headers = array($headers);
        $clearHeaderCalls = (int) $clearHeader;

        $this->_responseMock->expects($this->once())->method('getHeaders')->will($this->returnValue($headers));
        $this->_responseMock->expects($this->exactly($clearHeaderCalls))->method('clearHeader');

        $this->_model->extractContent($this->_requestMock, $this->_responseMock, $content);
    }

    public function removeMaintenanceHeaderDataProvider()
    {
        return array(
            '#no_headers' => array(array(), false),
            '#headers_without_maintenance' => array(
                array(
                    'name' => 'Location',
                    'value' => 'http://magento.com',
                    'replace' => true,
                ),
                false
            ),
            '#headers_without_replace' => array(
                array(
                    'name' => 'Location',
                    'value' => 'http://magento.com/maintenance.html',
                    'replace' => false,
                ),
                false
            ),
            '#headers_without_value' => array(
                array(
                    'name' => 'Location',
                    'value' => '',
                    'replace' => false,
                ),
                false
            ),
            '#headers_with_maintenance' => array(
                array(
                    'name' => 'Location',
                    'value' => 'http://magento.com/maintenance.html',
                    'replace' => true,
                ),
                true
            ),
        );
    }

    public function testExtractContentWithoutContent()
    {
        $content = false;
        $this->_responseMock->expects($this->once())->method('getHeaders')->will($this->returnValue(array()));
        $this->_configMock->expects($this->never())->method('isMaintenanceMode');
        $this->_responseMock->expects($this->never())->method('clearAllHeaders');

        $this->assertFalse($this->_model->extractContent($this->_requestMock, $this->_responseMock, $content));
    }

    public function testExtractContentWithoutMaintenanceMode()
    {
        $content = 'test_content';
        $this->_responseMock->expects($this->once())->method('getHeaders')->will($this->returnValue(array()));
        $this->_configMock->expects($this->once())->method('isMaintenanceMode')->will($this->returnValue(false));
        $this->_requestMock->expects($this->never())->method('getServer');
        $this->_responseMock->expects($this->never())->method('clearAllHeaders');

        $actual = $this->_model->extractContent($this->_requestMock, $this->_responseMock, $content);
        $this->assertEquals($content, $actual);
    }

    public function testExtractContentWithoutMaintenanceUrl()
    {
        $content = 'test_content';
        $this->_responseMock->expects($this->once())->method('getHeaders')->will($this->returnValue(array()));
        $this->_configMock->expects($this->once())->method('isMaintenanceMode')->will($this->returnValue(true));
        $this->_configMock->expects($this->once())->method('getUrl')->will($this->returnValue(null));
        $this->_requestMock->expects($this->never())->method('getServer');
        $this->_responseMock->expects($this->never())->method('clearAllHeaders');

        $actual = $this->_model->extractContent($this->_requestMock, $this->_responseMock, $content);
        $this->assertEquals($content, $actual);
    }

    public function testExtractContentIpInWhiteList()
    {
        $content = 'test_content';
        $this->_responseMock->expects($this->once())->method('getHeaders')->will($this->returnValue(array()));
        $this->_configMock->expects($this->once())->method('isMaintenanceMode')->will($this->returnValue(true));
        $this->_configMock->expects($this->once())->method('getUrl')->will($this->returnValue('http://localhost.com'));
        $this->_requestMock->expects($this->once())
            ->method('getServer')->with('REMOTE_ADDR')->will($this->returnValue('127.0.0.1'));
        $this->_configMock->expects($this->once())
            ->method('getWhiteList')->will($this->returnValue(array('127.0.0.1', '127.0.0.2')));

        $this->_responseMock->expects($this->never())->method('clearAllHeaders');

        $actual = $this->_model->extractContent($this->_requestMock, $this->_responseMock, $content);
        $this->assertEquals($content, $actual);
    }

    public function testExtractContentIpNotInWhiteListAndWithMaintenanceInUri()
    {
        $content = 'test_content';
        $this->_responseMock->expects($this->once())->method('getHeaders')->will($this->returnValue(array()));
        $this->_configMock->expects($this->once())->method('isMaintenanceMode')->will($this->returnValue(true));
        $this->_configMock->expects($this->once())->method('getUrl')->will($this->returnValue('http://localhost.com'));

        $valueMap = array(
            array('REMOTE_ADDR', null, '127.0.0.3'),
            array('REQUEST_URI', null, 'http://localhost.com/maintenance.html'),
        );

        $this->_requestMock->expects($this->exactly(2))
            ->method('getServer')->will($this->returnValueMap($valueMap));

        $this->_configMock->expects($this->once())
            ->method('getWhiteList')->will($this->returnValue(array('127.0.0.1', '127.0.0.2')));

        $this->_responseMock->expects($this->never())->method('clearAllHeaders');

        $actual = $this->_model->extractContent($this->_requestMock, $this->_responseMock, $content);
        $this->assertEquals($content, $actual);
    }

    public function testExtractContentClearHeaderAndRedirect()
    {
        $content = 'test_content';
        $this->_responseMock->expects($this->once())->method('getHeaders')->will($this->returnValue(array()));
        $this->_configMock->expects($this->once())->method('isMaintenanceMode')->will($this->returnValue(true));
        $this->_configMock->expects($this->any())->method('getUrl')->will($this->returnValue('http://localhost.com'));

        $valueMap = array(
            array('REMOTE_ADDR', null, '127.0.0.3'),
            array('REQUEST_URI', null, 'http://localhost.com/catalog.html'),
        );

        $this->_requestMock->expects($this->exactly(2))
            ->method('getServer')->will($this->returnValueMap($valueMap));

        $this->_configMock->expects($this->once())
            ->method('getWhiteList')->will($this->returnValue(array('127.0.0.1', '127.0.0.2')));

        $this->_responseMock->expects($this->once())->method('clearAllHeaders');
        $this->_responseMock->expects($this->once())->method('setRedirect')->with('http://localhost.com');

        $actual = $this->_model->extractContent($this->_requestMock, $this->_responseMock, $content);
        $this->assertEquals($content, $actual);
    }
}
