<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Centinel\Model\Service
 */
namespace Magento\Centinel\Model;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Centinel\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\Centinel\Model\Api
     */
    protected $_api;

    /**
     * @var \Magento\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Core\Model\Session\AbstractSession
     */
    protected $_centinelSession;

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Centinel\Model\StateFactory
     */
    protected $_stateFactory;

    /**
     * @var \Magento\Centinel\Model\Adminhtml\Service
     */
    protected $_model;

    /**
     * Build model
     */
    protected function setUp()
    {
        $this->_config = $this->getMock('Magento\Centinel\Model\Config', array(), array(), '', false);
        $this->_api = $this->getMock('Magento\Centinel\Model\Api', array(), array(), '', false);
        $this->_url = $this->_getUrlMock();
        $this->_centinelSession = $this->getMock('Magento\Core\Model\Session\AbstractSession',
            array(), array(), '', false
        );
        $this->_session = $this->getMock('Magento\Core\Model\Session', array(), array(), '', false);
        $this->_stateFactory = $this->getMock('Magento\Centinel\Model\StateFactory', array(), array(), '', false);

        $this->_model = new \Magento\Centinel\Model\Adminhtml\Service(
            $this->_config,
            $this->_api,
            $this->_url,
            $this->_centinelSession,
            $this->_session,
            $this->_stateFactory,
            array()
        );
    }

    /**
     * Create mock for URL model
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getUrlMock()
    {
        return $this->getMock('Magento\Core\Model\Url', array('getUrl'), array(), '', false);
    }

    /**
     * @covers \Magento\Centinel\Model\Service::_getUrl
     */
    public function testGetUrl()
    {
        $this->_url->expects($this->once())->method('getUrl')->will($this->returnValue('centinel/index/test'));
        $method = new \ReflectionMethod('Magento\Centinel\Model\Service', '_getUrl');
        $method->setAccessible(true);
        $this->assertEquals('adminhtml/centinel/url', $method->invoke($this->_model, 'test', array()));
    }
}
