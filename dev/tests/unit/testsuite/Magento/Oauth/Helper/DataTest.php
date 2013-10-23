<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Oauth\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Core\Helper\Context */
    protected $_coreContextMock;

    /** @var \Magento\Core\Model\Store\Config */
    protected $_storeConfigMock;

    /** @var \Magento\Oauth\Helper\Data */
    protected $_dataHelper;

    protected function setUp()
    {
        $this->_coreContextMock = $this->getMockBuilder('Magento\Core\Helper\Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_storeConfigMock = $this->getMockBuilder('Magento\Core\Model\Store\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_dataHelper = new \Magento\Oauth\Helper\Data(
            $this->_coreContextMock,
            $this->_storeConfigMock
        );
    }

    protected function tearDown()
    {
        unset($this->_coreContextMock);
        unset($this->_storeConfigMock);
        unset($this->_dataHelper);
    }

    public function testIsCleanupProbabilityZero()
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->will($this->returnValue(0));
        $this->assertFalse($this->_dataHelper->isCleanupProbability());
    }

    public function testIsCleanupProbabilityRandomOne()
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->will($this->returnValue(1));
        $this->assertTrue($this->_dataHelper->isCleanupProbability());
    }

    public function testGetCleanupExpirationPeriodZero()
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->will($this->returnValue(0));
        $this->assertEquals(
            \Magento\Oauth\Helper\Data::CLEANUP_EXPIRATION_PERIOD_DEFAULT,
            $this->_dataHelper->getCleanupExpirationPeriod()
        );
    }

    public function testGetCleanupExpirationPeriodNonZero()
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->will($this->returnValue(10));
        $this->assertEquals(10, $this->_dataHelper->getCleanupExpirationPeriod());
    }

    public function testConsumerPostMaxRedirectsZero()
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->will($this->returnValue(0));
        $this->assertEquals(0, $this->_dataHelper->getConsumerPostMaxRedirects());
    }

    public function testConsumerPostMaxRedirectsNonZero()
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->will($this->returnValue(10));
        $this->assertEquals(10, $this->_dataHelper->getConsumerPostMaxRedirects());
    }

    public function testGetConsumerPostTimeoutZero()
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->will($this->returnValue(0));
        $this->assertEquals(
            \Magento\Oauth\Helper\Data::CONSUMER_POST_TIMEOUT_DEFAULT,
            $this->_dataHelper->getConsumerPostTimeout()
        );
    }

    public function testGetConsumerPostTimeoutNonZero()
    {
        $this->_storeConfigMock->expects($this->once())->method('getConfig')
            ->will($this->returnValue(10));
        $this->assertEquals(10, $this->_dataHelper->getConsumerPostTimeout());
    }
}
