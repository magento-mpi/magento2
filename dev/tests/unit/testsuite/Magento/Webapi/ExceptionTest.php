<?php
/**
 * Test Webapi module exception.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_ExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Webapi exception construct.
     */
    public function testConstruct()
    {
        $code = 1111;
        $details = array('key1' => 'value1', 'key2' => 'value2');
        $apiException = new Magento_Webapi_Exception('Message', Magento_Webapi_Exception::HTTP_UNAUTHORIZED, $code, $details);
        $this->assertEquals(
            $apiException->getHttpCode(),
            Magento_Webapi_Exception::HTTP_UNAUTHORIZED,
            'Exception code is set incorrectly in construct.'
        );
        $this->assertEquals(
            $apiException->getMessage(),
            'Message',
            'Exception message is set incorrectly in construct.'
        );
        $this->assertEquals(
            $apiException->getCode(),
            $code,
            'Exception code is set incorrectly in construct.'
        );
        $this->assertEquals(
            $apiException->getDetails(),
            $details,
            'Details are set incorrectly in construct.'
        );
    }

    /**
     * Test Webapi exception construct with invalid data.
     *
     * @dataProvider providerForTestConstructInvalidCode
     */
    public function testConstructInvalidCode($code)
    {
        $this->setExpectedException('InvalidArgumentException', 'The specified HTTP code "' . $code . '" is invalid.');
        /** Create Magento_Webapi_Exception object with invalid code. */
        /** Valid codes range is from 400 to 599. */
        new Magento_Webapi_Exception('Message', $code);
    }

    public function testGetOriginatorSender()
    {
        $apiException = new Magento_Webapi_Exception('Message', Magento_Webapi_Exception::HTTP_UNAUTHORIZED);
        /** Check that Webapi Exception object with code 401 matches Sender originator.*/
        $this->assertEquals(
            Magento_Webapi_Model_Soap_Fault::FAULT_CODE_SENDER,
            $apiException->getOriginator(),
            'Wrong Sender originator detecting.'
        );
    }

    public function testGetOriginatorReceiver()
    {
        $apiException = new Magento_Webapi_Exception('Message', Magento_Webapi_Exception::HTTP_INTERNAL_ERROR);
        /** Check that Webapi Exception object with code 500 matches Receiver originator.*/
        $this->assertEquals(
            Magento_Webapi_Model_Soap_Fault::FAULT_CODE_RECEIVER,
            $apiException->getOriginator(),
            'Wrong Receiver originator detecting.'
        );
    }

    /**
     * Data provider for testConstructInvalidCode.
     *
     * @return array
     */
    public function providerForTestConstructInvalidCode()
    {
        return array(
            //Each array contains invalid Exception code.
            array(300),
            array(600),
        );
    }
}
