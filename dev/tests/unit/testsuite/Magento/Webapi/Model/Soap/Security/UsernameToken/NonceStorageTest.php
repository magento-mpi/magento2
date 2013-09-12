<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test SOAP WS-Security UsernameToken nonce & timestamp storage implementation.
 */
class Magento_Webapi_Model_Soap_Security_UsernameToken_NonceStorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Webapi\Model\Soap\Security\UsernameToken\NonceStorage
     */
    protected $_nonceStorage;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    /**
     * Set up cache instance mock and nonce storage object to be tested.
     */
    protected function setUp()
    {
        $this->_cacheMock = $this->getMock('Magento\Core\Model\CacheInterface');
        $this->_nonceStorage = new \Magento\Webapi\Model\Soap\Security\UsernameToken\NonceStorage($this->_cacheMock);
    }

    /**
     * Clean up.
     */
    protected function tearDown()
    {
        unset($this->_cacheMock);
        unset($this->_nonceStorage);
    }

    /**
     * @param int $timestamp
     * @dataProvider invalidTimestampDataProvider
     * @expectedException \Magento\Webapi\Model\Soap\Security\UsernameToken\TimestampRefusedException
     */
    public function testValidateNonceInvalidTimestamp($timestamp)
    {
        $this->_nonceStorage->validateNonce('', $timestamp);
    }

    public static function invalidTimestampDataProvider()
    {
        return array(
            'Timestamp is zero' => array(0),
            'Timestamp is a string' => array('abcdef'),
            'Timestamp is negative' => array(-1),
        );
    }

    public function testValidateNonceTimeStampIsTooOld()
    {
        $this->setExpectedException('Magento\Webapi\Model\Soap\Security\UsernameToken\TimestampRefusedException');
        $timestamp = time() - \Magento\Webapi\Model\Soap\Security\UsernameToken\NonceStorage::NONCE_TTL;
        $this->_nonceStorage->validateNonce('', $timestamp);
    }

    public function testValidateNonceTimeStampFromFuture()
    {
        $this->setExpectedException('Magento\Webapi\Model\Soap\Security\UsernameToken\TimestampRefusedException');
        /** Timestamp is from future more far than 60 seconds must be prohibited */
        $this->_nonceStorage->validateNonce('', time() + 65);
    }

    public function testValidateNonce()
    {
        $nonce = 'abc123';
        $timestamp = time();

        $this->_cacheMock
            ->expects($this->once())
            ->method('load')
            ->with($this->_nonceStorage->getNonceCacheId($nonce))
            ->will($this->returnValue(false));
        $this->_cacheMock
            ->expects($this->once())
            ->method('save')
            ->with($timestamp, $this->_nonceStorage->getNonceCacheId($nonce),
            array(\Magento\Webapi\Model\ConfigAbstract::WEBSERVICE_CACHE_TAG),
            \Magento\Webapi\Model\Soap\Security\UsernameToken\NonceStorage::NONCE_TTL
                + \Magento\Webapi\Model\Soap\Security\UsernameToken\NonceStorage::NONCE_FROM_FUTURE_ACCEPTABLE_RANGE);

        $this->_nonceStorage->validateNonce($nonce, $timestamp);
    }

    /**
     * @expectedException \Magento\Webapi\Model\Soap\Security\UsernameToken\NonceUsedException
     */
    public function testValidateNonceUsed()
    {
        $nonce = 'abc123';
        $timestamp = time();

        $this->_cacheMock
            ->expects($this->once())
            ->method('load')
            ->with($this->_nonceStorage->getNonceCacheId($nonce))
            ->will($this->returnValue($timestamp));

        $this->_nonceStorage->validateNonce($nonce, $timestamp);
    }
}
