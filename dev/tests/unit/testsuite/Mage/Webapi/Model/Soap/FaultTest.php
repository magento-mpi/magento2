<?php
/**
 * Test SOAP fault model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Soap_FaultTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Webapi_Model_Soap_Fault */
    protected $_soapFault;

    protected function setUp()
    {
        /** Initialize SUT. */
        $this->_soapFault = new Mage_Webapi_Model_Soap_Fault();
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_soapFault);
        parent::tearDown();
    }

    public function testToXmlDeveloperModeOff()
    {
        $expectedResult = <<<XML
<?xml version="1.0" encoding="utf-8" ?>
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope">
    <env:Body>
        <env:Fault>
            <env:Code>
                <env:Value>env:Receiver</env:Value>
            </env:Code>
            <env:Reason>
                <env:Text xml:lang="en">Internal Error.</env:Text>
            </env:Reason>
        </env:Fault>
    </env:Body>
</env:Envelope>
XML;
        $actualXml = $this->_soapFault->toXml(false);
        $this->assertXmlStringEqualsXmlString(
            $expectedResult,
            $actualXml,
            'Wrong SOAP fault message with default parameters.'
        );
    }

    public function testToXmlDeveloperModeOn()
    {
        $actualXml = $this->_soapFault->toXml(true);
        $this->assertContains('<m:Trace>', $actualXml, 'Exception trace is not found in XML.');
    }

    /**
     * Test getSoapFaultMessage method.
     *
     * @dataProvider dataProviderForGetSoapFaultMessageTest
     */
    public function testGetSoapFaultMessage(
        $faultReason,
        $faultCode,
        $language,
        $additionalParameters,
        $expectedResult,
        $assertMessage
    ) {
        $actualResult = $this->_soapFault->getSoapFaultMessage(
            $faultReason,
            $faultCode,
            $language,
            $additionalParameters
        );
        $this->assertXmlStringEqualsXmlString($expectedResult, $actualResult, $assertMessage);
    }

    /**
     * Data provider for GetSoapFaultMessage test.
     *
     * @return array
     */
    public function dataProviderForGetSoapFaultMessageTest()
    {
        /** Include file with all expected SOAP fault XMLs. */
        $expectedXmls = include __DIR__ . '/../../_files/soap_fault/soap_fault_expected_xmls.php';
        return array(
            //Each array contains data for SOAP Fault Message, Expected XML, and Assert Message.
            array(
                'Fault reason',
                'Sender',
                Mage_Webapi_Model_Soap_Fault::DEFAULT_LANGUAGE,
                array('key1' => 'value1', 'key2' => 'value2'),
                $expectedXmls['expectedResultArrayDataDetails'],
                'SOAP fault message with associated array data details is invalid.'
            ),
            array(
                'Fault reason',
                'Sender',
                Mage_Webapi_Model_Soap_Fault::DEFAULT_LANGUAGE,
                array('value1', 'value2'),
                $expectedXmls['expectedResultIndexArrayDetails'],
                'SOAP fault message with index array data details is invalid.'
            ),
            array(
                'Fault reason',
                'Sender',
                Mage_Webapi_Model_Soap_Fault::DEFAULT_LANGUAGE,
                array(),
                $expectedXmls['expectedResultEmptyArrayDetails'],
                'SOAP fault message with empty array data details is invalid.'
            ),
            array(
                'Fault reason',
                'Sender',
                Mage_Webapi_Model_Soap_Fault::DEFAULT_LANGUAGE,
                (object)array('key' => 'value'),
                $expectedXmls['expectedResultObjectDetails'],
                'SOAP fault message with object data details is invalid.'
            ),
            array(
                'Fault reason',
                'Sender',
                Mage_Webapi_Model_Soap_Fault::DEFAULT_LANGUAGE,
                array('key' => array('sub_key' => 'value')),
                $expectedXmls['expectedResultComplexDataDetails'],
                'SOAP fault message with complex data details is invalid.'
            ),
        );
    }

    public function testConstructor()
    {
        $message = "Soap fault reason.";
        $soapFault = new Mage_Webapi_Model_Soap_Fault(
            $message,
            Mage_Webapi_Model_Soap_Fault::FAULT_CODE_RECEIVER,
            Mage_Webapi_Model_Soap_Fault::DEFAULT_LANGUAGE,
            null,
            array('param1' => 'value1', 'param2' => 2),
            111
        );
        $actualXml = $soapFault->toXml();
        $expectedXml = <<<FAULT_XML
<?xml version="1.0" encoding="utf-8" ?>
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" xmlns:m="http://magento.com">
    <env:Body>
        <env:Fault>
            <env:Code>
                <env:Value>env:Receiver</env:Value>
            </env:Code>
            <env:Reason>
                <env:Text xml:lang="en">Soap fault reason.</env:Text>
            </env:Reason>
            <env:Detail>
                <m:ErrorDetails>
                    <m:Parameters>
                        <m:param1>value1</m:param1>
                        <m:param2>2</m:param2>
                    </m:Parameters>
                    <m:Code>111</m:Code>
                </m:ErrorDetails>
            </env:Detail>
        </env:Fault>
    </env:Body>
</env:Envelope>
FAULT_XML;
        $this->assertXmlStringEqualsXmlString($expectedXml, $actualXml, "Soap fault is invalid.");
    }
}
