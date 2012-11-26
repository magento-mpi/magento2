<?php
/**
 * Test SOAP fault model.
 *
 * @copyright {}
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

    /**
     * Test public function getSoapFaultMessage method with default parameters.
     */
    public function testGetSoapFaultMessageDefaultParameters()
    {
        $expectedResult = <<<XML
<?xml version="1.0" encoding="utf-8" ?>
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope">
    <env:Body>
        <env:Fault>
            <env:Code>
                <env:Value>Receiver</env:Value>
            </env:Code>
            <env:Reason>
                <env:Text xml:lang="en">Internal Error.</env:Text>
            </env:Reason>
        </env:Fault>
    </env:Body>
</env:Envelope>
XML;
        $this->assertXmlStringEqualsXmlString(
            $expectedResult,
            $this->_soapFault->getSoapFaultMessage(),
            'Wrong soap fault message with default parameters.'
        );
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
        /** Include file with all expected soap fault XMLs. */
        $expectedXmls = include __DIR__ . '/../../_files/soap_fault/soap_fault_expected_xmls.php';
        return array(
            //Each array contains data for SOAP Fault Message, Expected XML and Assert Message.
            array(
                'Fault reason',
                'Fault code',
                'cn',
                array('key1' => 'value1', 'key2' => 'value2'),
                $expectedXmls['expectedResultArrayDataDetails'],
                'Wrong soap fault message with associated array data details.'
            ),
            array(
                'Fault reason',
                'Fault code',
                'en',
                array('value1', 'value2'),
                $expectedXmls['expectedResultIndexArrayDetails'],
                'Wrong soap fault message with index array data details.'
            ),
            array(
                'Fault reason',
                'Fault code',
                'en',
                array(),
                $expectedXmls['expectedResultEmptyArrayDetails'],
                'Wrong soap fault message with empty array data details.'
            ),
            array(
                'Fault reason',
                'Fault code',
                'en',
                (object)array('key' => 'value'),
                $expectedXmls['expectedResultObjectDetails'],
                'Wrong soap fault message with object data details.'
            ),
            array(
                'Fault reason',
                'Fault code',
                'en',
                'String details',
                $expectedXmls['expectedResultStringDetails'],
                'Wrong soap fault message with string data details.'
            ),
            array(
                'Fault reason',
                'Fault code',
                'en',
                array('key' => array('sub_key' => 'value')),
                $expectedXmls['expectedResultComplexDataDetails'],
                'Wrong soap fault message with complex data details.'
            ),
        );
    }
}
