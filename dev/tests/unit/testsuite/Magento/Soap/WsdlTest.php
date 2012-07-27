<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Soap
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test case for Magento_Soap_Wsdl library.
 */
class Magento_Soap_WsdlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider dataPositiveWsdl
     * @param $validXml
     */
    public function testAddBinding($validXml)
    {
        $wsdl = new Magento_Soap_Wsdl($validXml);
        $binding = $wsdl->addBinding('testBinding', 'testPortType');

        $this->assertInstanceOf('DOMElement', $binding);
        $bindings = $wsdl->getDom()->getElementsByTagName('binding');
        $this->assertEquals(1, count($bindings));
    }

    /**
     * @dataProvider dataPositiveWsdl
     * @param $validXml
     */
    public function testAddBindingOperation($validXml)
    {
        $wsdl = new Magento_Soap_Wsdl($validXml);
        $binding = $wsdl->addBinding('testBinding', 'testPortType');
        $operation = $wsdl->addBindingOperation($binding, 'testOperation', array('use' => 'literal'),
            array('use' => 'literal'), array('name' => 'testFault', 'use' => 'literal'));

        $this->assertInstanceOf('DOMElement', $operation);
    }

    /**
     * @dataProvider dataPositiveWsdl
     * @param $validXml
     */
    public function testAddSoapBinding($validXml)
    {
        $wsdl = new Magento_Soap_Wsdl($validXml);
        $binding = $wsdl->addBinding('testBinding', 'testPortType');
        $soapBinding = $wsdl->addSoapBinding($binding);

        $this->assertInstanceOf('DOMElement', $soapBinding);
    }

    /**
     * @dataProvider dataPositiveWsdl
     * @param $validXml
     */
    public function testAddSoapOperation($validXml)
    {
        $wsdl = new Magento_Soap_Wsdl($validXml);
        $binding = $wsdl->addBinding('testBinding', 'testPortType');
        $soapOperation = $wsdl->addSoapOperation($binding, 'testSoapAction');

        $this->assertInstanceOf('DOMElement', $soapOperation);
    }

    /**
     * @dataProvider dataPositiveWsdl
     * @param $validXml
     */
    public function testAddService($validXml)
    {
        $wsdl = new Magento_Soap_Wsdl($validXml);
        $service = $wsdl->addService('TestService');

        $this->assertInstanceOf('DOMElement', $service);
    }

    /**
     * @dataProvider dataPositiveWsdl
     * @param $validXml
     */
    public function testAddServicePort($validXml)
    {
        $wsdl = new Magento_Soap_Wsdl($validXml);
        $service = $wsdl->addService('TestService');
        $servicePort = $wsdl->addServicePort($service, 'testPort', 'testBinding', 'http://test.location/');

        $this->assertInstanceOf('DOMElement', $servicePort);
    }

    public function dataPositiveWsdl()
    {
        $validXml = file_get_contents(__DIR__ . '/_files/positive/wsdl.xml');
        return array(
            array($validXml),
        );
    }
}
