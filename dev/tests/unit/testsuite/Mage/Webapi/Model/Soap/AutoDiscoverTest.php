<?php
use Zend\Soap\Wsdl;

/**
 * Tests for Mage_Webapi_Model_Soap_AutoDiscover.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Soap_AutoDiscoverTest extends PHPUnit_Framework_TestCase
{
    /**  @var Mage_Webapi_Model_Soap_AutoDiscover */
    protected $_autoDiscover;

    /**  @var Mage_Webapi_Model_Soap_Wsdl_Generator */
    protected $_wsdlGenerator;

    /**  @var Mage_Core_Model_CacheInterface */
    protected $_cache;

    protected function setUp()
    {
        $this->_wsdlGenerator = $this->getMockBuilder('Mage_Webapi_Model_Soap_Wsdl_Generator')
            ->disableOriginalConstructor()->getMock();
        $this->_cache = $this->getMockBuilder('Mage_Core_Model_CacheInterface')->disableOriginalConstructor()
            ->getMock();
        $this->_autoDiscover = new Mage_Webapi_Model_Soap_AutoDiscover(
            $this->_cache,
            $this->_wsdlGenerator
        );

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_wsdlGenerator);
        unset($this->_autoDiscover);
        unset($this->_cache);
        parent::tearDown();
    }

    /**
     * Test success case for handle
     */
    public function testHandleSuccess()
    {
        $genWSDL = 'generatedWSDL';
        $requestedService = array('catalogProduct' => 'V1');
        $this->_wsdlGenerator->expects($this->once())->method('generate')->will($this->returnValue($genWSDL));
        $this->assertEquals($genWSDL, $this->_autoDiscover->handle($requestedService, 'http://magento.host'));
    }
}
