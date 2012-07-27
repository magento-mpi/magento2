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
 *
 * @group validator
 */
class Magento_Soap_WsdlTest extends PHPUnit_Framework_TestCase
{
    public function testAddBinding()
    {
        $validXml = file_get_contents(__DIR__ . '/_files/positive/wsdl.xml');
        $wsdl = new Magento_Soap_Wsdl($validXml);
        $binding = $wsdl->addBinding('testBinding', 'testPortType');

        $this->assertInstanceOf('DOMElement', $binding);
        $bindings = $wsdl->toDom()->getElementsByTagName('binding');
        $this->assertEquals(1, count($bindings));
    }

    public function testAddBindingOperation()
    {
        $validXml = file_get_contents(__DIR__ . '/_files/positive/wsdl.xml');
        $wsdl = new Magento_Soap_Wsdl($validXml);
        $binding = $wsdl->addBinding('testBinding', 'testPortType');
        $operation = $wsdl->addBindingOperation($binding, 'testOperation', array('use' => 'literal'),
            array('use' => 'literal'), array('name' => 'testFault', 'use' => 'literal'));

        $this->assertInstanceOf('DOMElement', $operation);
    }

    public function testAddSoapBinding()
    {
        $validXml = file_get_contents(__DIR__ . '/_files/positive/wsdl.xml');
        $wsdl = new Magento_Soap_Wsdl($validXml);
        $binding = $wsdl->addBinding('testBinding', 'testPortType');
        $soapBinding = $wsdl->addSoapBinding($binding);

        $this->assertInstanceOf('DOMElement', $soapBinding);
    }

    public function testAddSoapOperation()
    {
        $validXml = file_get_contents(__DIR__ . '/_files/positive/wsdl.xml');
        $wsdl = new Magento_Soap_Wsdl($validXml);
        $binding = $wsdl->addBinding('testBinding', 'testPortType');
        $soapOperation = $wsdl->addSoapOperation($binding, 'testSoapAction');

        $this->assertInstanceOf('DOMElement', $soapOperation);
    }

    public function testAddService()
    {
        $validXml = file_get_contents(__DIR__ . '/_files/positive/wsdl.xml');
        $wsdl = new Magento_Soap_Wsdl($validXml);
        $service = $wsdl->addService('TestService', 'testPort', 'testBinding', 'http://test.location/');

        $this->assertInstanceOf('DOMElement', $service);
    }
}
