<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_PageCache_Model_ProcessorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $arguments = array(
            'scopeCode' => 'website',
            'restriction' => $this->getMock('Enterprise_PageCache_Model_Processor_RestrictionInterface'),
            'fpcCache' => $this->getMock('Enterprise_PageCache_Model_Cache', array(), array(), '', false),
            'designPackage' => $this->getMock('Mage_Core_Model_Design_Package_Proxy', array(), array(), '', false),
            'subProcessorFactory' => $this->getMock('Enterprise_PageCache_Model_Cache_SubProcessorFactory',
                array(), array(), '', false
            ),
            'placeholderFactory' => $this->getMock('Enterprise_PageCache_Model_Container_PlaceholderFactory',
                array(), array(), '', false
            ),
            'containerFactory' => $this->getMock('Enterprise_PageCache_Model_ContainerFactory',
                array(), array(), '', false
            ),
            'environment' => $this->getMock('Enterprise_PageCache_Model_Environment', array(), array(), '', false),
            'maintenanceConfig' => $this->getMock('Saas_Saas_Model_Maintenance_Config', array(), array(), '', false),
        );

        $methods = array(
            '_createRequestIds', '_beforeExtractContent', '_applyDesignChange', '_checkDesignException', 'isAllowed'
        );
        $this->_model = $this->getMock('Saas_PageCache_Model_Processor', $methods, $arguments);
    }

    public function testRemoveMaintenanceHeaderWithoutLocationHeader()
    {
        $requestMock = $this->getMock('Zend_Controller_Request_Http', array(), array(), '', false);
        $responseMock = $this->getMock('Zend_Controller_Response_Http', array(), array(), '', false);
        $content = false;

        $headers = array(
            array(
                'name' => 'Content-type',
                'value' => 'text/html',
                'replace' => true,
            )
        );

        $responseMock->expects($this->once())->method('getHeaders')->will($this->returnValue($headers));
        $responseMock->expects($this->never())->method('clearHeader');

        $this->_model->expects($this->once())->method('isAllowed')->will($this->returnValue(false));
        $this->_model->expects($this->once())->method('_checkDesignException')->will($this->returnValue(true));
        $this->_model->extractContent($requestMock, $responseMock, $content);
    }
}
