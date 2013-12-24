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

use Magento\Outbound\Authentication\Factory as AuthenticationFactory;
use Magento\Outbound\EndpointInterface;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var AuthenticationFactory */
    protected $_authFactory;

    protected function setUp()
    {
        $this->_authFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Outbound\Authentication\Factory', array(
                    'authenticationMap' => array(
                        EndpointInterface::AUTH_TYPE_HMAC => 'Magento\Outbound\Authentication\Hmac'
                    )
                ));
    }

    public function testGetFormatter()
    {
        $authObject = $this->_authFactory->getAuthentication(EndpointInterface::AUTH_TYPE_HMAC);
        $this->assertInstanceOf('Magento\Outbound\Authentication\Hmac', $authObject);
    }

    public function testGetFormatterIsCached()
    {
        $authObject = $this->_authFactory->getAuthentication(EndpointInterface::AUTH_TYPE_HMAC);
        $authObject2 = $this->_authFactory->getAuthentication(EndpointInterface::AUTH_TYPE_HMAC);
        $this->assertSame($authObject, $authObject2);
    }
}
