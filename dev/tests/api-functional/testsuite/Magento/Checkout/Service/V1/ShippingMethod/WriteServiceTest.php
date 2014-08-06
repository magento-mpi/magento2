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

    /**
     * @var \Magento\Sales\Model\Quote
     */
    protected $quote;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->quote = $this->objectManager->create('\Magento\Sales\Model\Quote');
    }

    protected function getServiceInfo()
    {
        return array(
            'rest' => array(
                'resourcePath' => '/V1/carts/' . $this->quote->getId() . '/selected-shipping-method',
                'httpMethod' => RestConfig::HTTP_METHOD_PUT,
            ),
            'soap' => array(
                'service' => 'checkoutShippingMethodWriteServiceV1',
                'serviceVersion' => 'V1',
                'operation' => 'checkoutShippingMethodWriteServiceV1SetMethod',
            ),
        );
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     */
    public function testSetMethod()
    {
        $this->quote->load('test_order_1', 'reserved_order_id');
        $serviceInfo = $this->getServiceInfo();

        $requestData = array(
            'cartId' => $this->quote->getId(),
            'carrierCode' => 'flatrate',
            'methodCode' => 'flatrate'
        );
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals(true, $result);
    }

    /**
     * @magentoApiDataFixture Magento/Checkout/_files/quote_with_address_saved.php
     * @expectedExceptionMessage Carrier with such method not found
     */
    public function testSetMethodWrongMethod()
    {
        $this->quote->load('test_order_1', 'reserved_order_id');
        $serviceInfo = $this->getServiceInfo();

        $requestData = array(
            'cartId' => $this->quote->getId(),
            'carrierCode' => 'flatrate',
            'methodCode' => 'wrongMethod'
        );
        try {
            $this->_webApiCall($serviceInfo, $requestData);
        } catch(\SoapFault $e) {
            $message = $e->getMessage();
        } catch(\Exception $e) {
            $message = json_decode($e->getMessage())->message;
        }
        $this->assertEquals('Carrier with such method not found: flatrate, wrongMethod', $message);

    }
}
