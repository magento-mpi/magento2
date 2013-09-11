<?php
/**
 * Test SOAP fault model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Soap_FaultTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Model\Soap\Fault */
    protected $_soapFault;

    protected function setUp()
    {
        /** Initialize SUT. */
        $this->_soapFault = new \Magento\Webapi\Model\Soap\Fault();
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
        $this->assertContains('<ExceptionTrace>', $actualXml, 'Exception trace is not found in XML.');
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
                'cn',
                array('key1' => 'value1', 'key2' => 'value2'),
                $expectedXmls['expectedResultArrayDataDetails'],
                'SOAP fault message with associated array data details is invalid.'
            ),
            array(
                'Fault reason',
                'Sender',
                'en',
                array('value1', 'value2'),
                $expectedXmls['expectedResultIndexArrayDetails'],
                'SOAP fault message with index array data details is invalid.'
            ),
            array(
                'Fault reason',
                'Sender',
                'en',
                array(),
                $expectedXmls['expectedResultEmptyArrayDetails'],
                'SOAP fault message with empty array data details is invalid.'
            ),
            array(
                'Fault reason',
                'Sender',
                'en',
                (object)array('key' => 'value'),
                $expectedXmls['expectedResultObjectDetails'],
                'SOAP fault message with object data details is invalid.'
            ),
            array(
                'Fault reason',
                'Sender',
                'en',
                'String details',
                $expectedXmls['expectedResultStringDetails'],
                'SOAP fault message with string data details is invalid.'
            ),
            array(
                'Fault reason',
                'Sender',
                'en',
                array('key' => array('sub_key' => 'value')),
                $expectedXmls['expectedResultComplexDataDetails'],
                'SOAP fault message with complex data details is invalid.'
            ),
        );
    }
}
