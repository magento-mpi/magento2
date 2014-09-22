<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Handler\OrderInjectable;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Handler\Curl as AbstractCurl;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;

/**
 * Class Curl
 * Create new order via curl
 */
class Curl extends AbstractCurl implements OrderInjectableInterface
{
    /**
     * Customer fixture
     *
     * @var CustomerInjectable
     */
    protected $customer;

    /**
     * Customer fixture
     *
     * @var OrderInjectable
     */
    protected $order;

    /**
     * Mapping values for data.
     *
     * @var array
     */
    protected $mappingData = [
        'region_id' => [
            'California' => '12',
        ],
        'country_id' => [
            'United States' => 'US'
        ]
    ];

    /**
     * Steps for create order on backend
     *
     * @var array
     */
    protected $steps = [
        'customer_choice' => 'header,data',
        'products_choice' => 'search,items,shipping_method,totals,giftmessage,billing_method',
        'apply_coupon_code' => 'items,shipping_method,totals,billing_method',
        'shipping_data_address' => 'shipping_method,billing_method,shipping_address,totals,giftmessage',
        'shipping_data_method_get' => 'shipping_method,totals',
        'shipping_data_method_set' => 'shipping_method,totals,billing_method',
    ];

    /**
     * Post request for creating order
     *
     * @param FixtureInterface|null $fixture [optional]
     * @return array
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $this->order = $fixture;
        $this->customer = $fixture->getDataFieldConfig('customer_id')['source']->getCustomer();
        $data = $this->replaceMappingData($this->prepareData($fixture));
        return ['id' => $this->createOrder($data)];
    }

    /**
     * Prepare POST data for creating product request
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture)
    {
        $result = [];
        $data = $fixture->getData();
        $result['customer_choice'] = $this->prepareCustomerData($data);
        $result['products_choice'] = $this->prepareProductsData($data['entity_id']);
        if (isset($data['coupon_code'])) {
            $result['apply_coupon_code'] = $this->prepareCouponCode($data['coupon_code']);
        }
        $result['order_data'] = $this->prepareOrderData($data);
        $result['shipping_data_address'] = $this->prepareShippingData($result['order_data']);
        $result['shipping_data_method_get'] = [
            'payment' => $data['payment_auth_expiration'],
            'collect_shipping_rates' => 1
        ];
        $result['shipping_data_method_set'] = [
            'order' => ['shipping_method' => $data['shipping_method']],
            'payment' => $data['payment_auth_expiration']
        ];

        return $result;
    }

    /**
     * Prepare coupon data
     *
     * @param SalesRuleInjectable $data
     * @return array
     */
    protected function prepareCouponCode(SalesRuleInjectable $data)
    {
        return ['order' => ['coupon' => ['code' => $data->getCouponCode()]]];
    }

    /**
     * Prepare shipping data
     *
     * @param array $data
     * @return array
     */
    protected function prepareShippingData(array $data)
    {
        $result = [
            'order' => [
                'billing_address' => $data['billing_address'],
            ],
            'payment' => $this->order->getPaymentAuthExpiration(),
            'reset_shipping' => 1,
            'shipping_as_billing' => 1,
        ];
        return $result;
    }

    /**
     * Prepare products data
     *
     * @param array $data
     * @return array
     */
    protected function prepareProductsData(array $data)
    {
        $result['item'] = [];
        foreach ($data['products'] as $value) {
            if (!$value->hasData('checkout_data')) {
                continue;
            }
            $methodName = 'prepare' . ucfirst($value->getDataConfig()['type_id']) . 'Data';
            if (!method_exists($this, $methodName)) {
                $methodName = 'prepareSimpleData';
            }
            $result['item'][$value->getId()] = $this->$methodName($value);
        }
        return $result;
    }

    /**
     * Prepare data for configurable product
     *
     * @param FixtureInterface $product
     * @return array
     */
    protected function prepareConfigurableData(FixtureInterface $product)
    {
        $result = [];
        $checkoutData = $product->getCheckoutData();
        $result['qty'] = $checkoutData['qty'];
        $attributesData = $product->hasData('configurable_attributes_data')
            ? $product->getDataFieldConfig('configurable_attributes_data')['source']->getAttributesData()
            : null;
        if ($attributesData == null) {
            return $result;
        }
        foreach ($checkoutData['configurable_options'] as $option) {
            $attributeId = $attributesData[$option['title']]['attribute_id'];
            $optionId = $attributesData[$option['title']]['options'][$option['value']]['id'];
            $result['super_attribute'][$attributeId] = $optionId;
        }

        return $result;
    }

    /**
     * Prepare data for simple product
     *
     * @param FixtureInterface $product
     * @return array
     */
    protected function prepareSimpleData(FixtureInterface $product)
    {
        return ['qty' => $product->getCheckoutData()['qty']];
    }

    /**
     * Prepare order data
     *
     * @param array $data
     * @return array
     */
    protected function prepareOrderData(array $data)
    {
        $customerGroupId = $this->customer->hasData('group_id')
            ? $this->customer->getDataFieldConfig('group_id')['source']->getCustomerGroup()->getCustomerGroupId()
            : 1;
        $result = [
            'name' => $this->customer->getFirstname(),
            'order' => [
                'currency' => $data['order_currency_code'],
                'account' => [
                    'group_id' => $customerGroupId,
                    'email' => $this->customer->getEmail()
                ],
                'shipping_method' => $data['shipping_method']
            ],
            'item' => $this->prepareOrderProductsData($data['entity_id']),
            'billing_address' => $this->prepareBillingAddress($data['billing_address_id']),
            'shipping_same_as_billing' => 'on',
            'payment' => $data['payment_auth_expiration'],

        ];

        return $result;
    }

    /**
     * Prepare customer data
     *
     * @param array $data
     * @return array
     */
    protected function prepareCustomerData(array $data)
    {
        return [
            'currency_id' => $data['base_currency_code'],
            'customer_id' => $this->customer->getData('id'),
            'payment' => $data['payment_authorization_amount'],
            'store_id' => $this->order->getDataFieldConfig('store_id')['source']->store->getStoreId()
        ];
    }

    /**
     * Prepare order products data
     *
     * @param array $data
     * @return array
     */
    protected function prepareOrderProductsData(array $data)
    {
        $result = [];
        foreach ($data['products'] as $value) {
            $result[$value->getId()] = [
                'qty' => ['qty' => $value->getCheckoutData()['qty']],
            ];
        }

        return $result;
    }

    /**
     * Prepare billing address data
     *
     * @param array $data
     * @return array
     */
    protected function prepareBillingAddress(array $data)
    {
        $result = $data;
        $result['firstname'] = $this->customer->getFirstname();
        $result['lastname'] = $this->customer->getLastname();

        return $result;
    }

    /**
     * Create product via curl
     *
     * @param array $data
     * @return int|null
     * @throws \Exception
     */
    protected function createOrder(array $data)
    {
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        foreach ($this->steps as $key => $step) {
            if (!isset($data[$key])) {
                continue;
            }
            $url = $_ENV['app_backend_url'] . 'sales/order_create/loadBlock/block/' . $step . '?isAjax=true';
            $curl->write(CurlInterface::POST, $url, '1.1', [], $data[$key]);
            $curl->read();
        }
        $url = $_ENV['app_backend_url'] . 'sales/order_create/save';
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.1', [], $data['order_data']);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Order creation by curl handler was not successful! Response: $response");
        }
        preg_match("~<h1 class=\"title\">#(.*)</h1>~", $response, $matches);

        return isset($matches[1]) ? $matches[1] : null;
    }
}
