<?php
/**
 * \Magento\Outbound\Authentication\Factory
 *
 * {license_notice}
 *
 * @copyright          {copyright}
 * @license            {license_link}
 */
namespace Magento\Outbound\Authentication;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Outbound\Authentication\Factory */
    protected $_authFactory;

    public function setUp()
    {
        $this->_authFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Outbound\Authentication\Factory');
    }

    public function testGetFormatter()
    {
        $authObject = $this->_authFactory->getAuthentication(\Magento\Outbound\EndpointInterface::AUTH_TYPE_HMAC);
        $this->assertInstanceOf('Magento\Outbound\Authentication\Hmac', $authObject);
    }

    public function testGetFormatterIsCached()
    {
        $authObject = $this->_authFactory->getAuthentication(\Magento\Outbound\EndpointInterface::AUTH_TYPE_HMAC);
        $authObject2 = $this->_authFactory->getAuthentication(\Magento\Outbound\EndpointInterface::AUTH_TYPE_HMAC);
        $this->assertSame($authObject, $authObject2);
    }
}
