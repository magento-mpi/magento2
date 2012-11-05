<?php
/**
 * SOAP WSDL class tests.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Soap_WsdlTest extends PHPUnit_Framework_TestCase
{
    public function testGetTargetNamespace()
    {
        $testURL = 'http://magen.host/api/soap/?wsdl';
        $wsdl = new Mage_Webapi_Model_Soap_Wsdl('TestWSDL', $testURL);

        $this->assertEquals('urn:Magento-' . md5($testURL), $wsdl->getTargetNamespace());
    }
}
