<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\ShippingMethod;

use \Magento\TestFramework\TestCase\WebapiAbstract;
use \Magento\TestFramework\ObjectManager;
use \Magento\Webapi\Model\Rest\Config as RestConfig;

class WriteServiceTest extends WebapiAbstract
{
    /**
     * @var ObjectManager
     */
    private $objectManager;


    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     */
    public function testSetMethod()
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->objectManager->create('\Magento\Sales\Model\Quote');
        $quote->load('test_order_1', 'reserved_order_id');
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/carts/' . $quote->getId() . '/selected-shipping-method',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
            ),
            'soap' => array(
                'service' => 'checkoutShippingMethodWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'checkoutShippingMethodWriteServiceV1SetMethod',
            ),
        );

        $requestData = array(
            'cartId' => $quote->getId(),
            'carrierCode' => 'flatrate',
            'methodCode' => 'flatrate'
        );
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(true, $result);
    }
}
