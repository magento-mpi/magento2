<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\TestFramework\TestCase\WebapiAbstract,
    Magento\Webapi\Model\Rest\Config as RestConfig,
    Magento\Sales\Service\V1\Data\OrderAddress;

/**
 * Class OrderAddressUpdateTest
 * @package Magento\Sales\Service\V1
 */
class OrderAddressUpdateTest extends WebapiAbstract
{
    const SERVICE_VERSION = 'V1';

    const SERVICE_NAME = 'salesOrderWriteV1';

    /**
     * @magentoApiDataFixture Magento/Sales/_files/order.php
     */
    public function testOrderAddressUpdate()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId('100000001');

        $address = [
            OrderAddress::REGION => 'CA',
            OrderAddress::POSTCODE => 'postcode',
            OrderAddress::LASTNAME => 'lastname',
            OrderAddress::STREET => 'street',
            OrderAddress::CITY => 'city',
            OrderAddress::EMAIL => 'email@emai.com',
            OrderAddress::COMPANY => 'company',
            OrderAddress::TELEPHONE => 't123456789',
            OrderAddress::COUNTRY_ID => 'US',
            OrderAddress::FIRSTNAME => 'firstname',
            OrderAddress::ADDRESS_TYPE => 'billing',
            OrderAddress::PARENT_ID => $order->getId(),
            OrderAddress::ENTITY_ID => $order->getBillingAddressId(),
            OrderAddress::CUSTOMER_ADDRESS_ID => null,
            OrderAddress::CUSTOMER_ID => null,
            OrderAddress::FAX => null,
            OrderAddress::MIDDLENAME => null,
            OrderAddress::PREFIX => null,
            OrderAddress::QUOTE_ADDRESS_ID => null,
            OrderAddress::REGION_ID => null,
            OrderAddress::SUFFIX => null,
            OrderAddress::VAT_ID => null,
            OrderAddress::VAT_IS_VALID => null,
            OrderAddress::VAT_REQUEST_DATE => null,
            OrderAddress::VAT_REQUEST_ID => null,
            OrderAddress::VAT_REQUEST_SUCCESS => null,

        ];
        $requestData = ['orderAddress' => $address];

        $serviceInfo = [
            'rest' => [
                'resourcePath' => '/V1/order/' . $order->getId(),
                'httpMethod' => RestConfig::HTTP_METHOD_PUT
            ],
            'soap' => [
                'service' => self::SERVICE_NAME,
                'serviceVersion' => self::SERVICE_VERSION,
                'operation' => self::SERVICE_NAME . 'addressUpdate'
            ]
        ];
        $result = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertTrue($result);
        /** @var \Magento\Sales\Model\Order $actualOrder */
        $actualOrder = $objectManager->get('Magento\Sales\Model\Order')->load($order->getId());
        $billingAddress = $actualOrder->getBillingAddress();

        $validate = [
            OrderAddress::REGION => 'CA',
            OrderAddress::POSTCODE => 'postcode',
            OrderAddress::LASTNAME => 'lastname',
            OrderAddress::STREET => 'street',
            OrderAddress::CITY => 'city',
            OrderAddress::EMAIL => 'email@emai.com',
            OrderAddress::COMPANY => 'company',
            OrderAddress::TELEPHONE => 't123456789',
            OrderAddress::COUNTRY_ID => 'US',
            OrderAddress::FIRSTNAME => 'firstname',
            OrderAddress::ADDRESS_TYPE => 'billing',
            OrderAddress::PARENT_ID => $order->getId(),
            OrderAddress::ENTITY_ID => $order->getBillingAddressId()
        ];
        foreach ($validate as $key => $field) {
            $this->assertEquals($validate[$key], $billingAddress->getData($key));
        }
    }
}
