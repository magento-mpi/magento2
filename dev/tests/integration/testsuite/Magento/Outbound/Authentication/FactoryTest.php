<?php
/**
 * Magento_Outbound_Authentication_Factory
 *
 * {license_notice}
 *
 * @copyright          {copyright}
 * @license            {license_link}
 */
class Magento_Outbound_Authentication_FactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Outbound_Authentication_Factory */
    protected $_authFactory;

    public function setUp()
    {
        $this->_authFactory = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Outbound_Authentication_Factory');
    }

    public function testGetFormatter()
    {
        $authObject = $this->_authFactory->getAuthentication(Magento_Outbound_EndpointInterface::AUTH_TYPE_HMAC);
        $this->assertInstanceOf('Magento_Outbound_Authentication_Hmac', $authObject);
    }

    public function testGetFormatterIsCached()
    {
        $authObject = $this->_authFactory->getAuthentication(Magento_Outbound_EndpointInterface::AUTH_TYPE_HMAC);
        $authObject2 = $this->_authFactory->getAuthentication(Magento_Outbound_EndpointInterface::AUTH_TYPE_HMAC);
        $this->assertSame($authObject, $authObject2);
    }
}
