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
     * @dataProvider dataProviderBaseDomDocument
     * @param $baseDomDocument
     */
    public function testAddBinding($baseDomDocument)
    {
        $wsdl = new Magento_Soap_Wsdl($baseDomDocument);
        $binding = $wsdl->addBinding('testBinding', 'testPortType');

        $this->assertInstanceOf('DOMElement', $binding);
        $this->assertEquals($binding->getAttribute('name'), 'testBinding');
    }

    /**
     * @dataProvider dataProviderBaseDomDocument
     * @param $baseDomDocument
     */
    public function testAddBindingOperation($baseDomDocument)
    {
        $wsdl = new Magento_Soap_Wsdl($baseDomDocument);
        $binding = $wsdl->addBinding('testBinding', 'testPortType');
        $operation = $wsdl->addBindingOperation($binding, 'testOperation', array('use' => 'literal'),
            array('use' => 'literal'), array('name' => 'testFault', 'use' => 'literal'));

        $this->assertInstanceOf('DOMElement', $operation);
        $this->assertEquals($operation->getAttribute('name'), 'testOperation');
    }

    /**
     * @dataProvider dataProviderBaseDomDocument
     * @param $baseDomDocument
     */
    public function testAddSoapBinding($baseDomDocument)
    {
        $wsdl = new Magento_Soap_Wsdl($baseDomDocument);
        $binding = $wsdl->addBinding('testBinding', 'testPortType');
        $soapBinding = $wsdl->addSoapBinding($binding);

        $this->assertInstanceOf('DOMElement', $soapBinding);
    }

    /**
     * @dataProvider dataProviderBaseDomDocument
     * @param $baseDomDocument
     */
    public function testAddSoapOperation($baseDomDocument)
    {
        $wsdl = new Magento_Soap_Wsdl($baseDomDocument);
        $binding = $wsdl->addBinding('testBinding', 'testPortType');
        $soapOperation = $wsdl->addSoapOperation($binding, 'testSoapAction');

        $this->assertInstanceOf('DOMElement', $soapOperation);
    }

    /**
     * @dataProvider dataProviderBaseDomDocument
     * @param $baseDomDocument
     */
    public function testAddService($baseDomDocument)
    {
        $wsdl = new Magento_Soap_Wsdl($baseDomDocument);
        $service = $wsdl->addService('TestService');

        $this->assertInstanceOf('DOMElement', $service);
    }

    /**
     * @dataProvider dataProviderBaseDomDocument
     * @param $baseDomDocument
     */
    public function testAddServicePort($baseDomDocument)
    {
        $wsdl = new Magento_Soap_Wsdl($baseDomDocument);
        $service = $wsdl->addService('TestService');
        $servicePort = $wsdl->addServicePort($service, 'testPort', 'testBinding', 'http://test.location/');

        $this->assertInstanceOf('DOMElement', $servicePort);
    }

    /**
     * @dataProvider dataProviderBaseDomDocument
     * @param $baseDomDocument
     */
    public function testToXml($baseDomDocument)
    {
        $wsdl = new Magento_Soap_Wsdl($baseDomDocument);
        $xml = $wsdl->toXml();
        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', $xml);
    }

    public function dataProviderBaseDomDocument()
    {
        $baseDomDocument = new DOMDocument();
        $baseDomDocument->load(__DIR__ . '/_files/positive/wsdl.xml');
        return array(
            array($baseDomDocument),
        );
    }
}
