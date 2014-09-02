<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Handler\Order;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Handler\Curl as AbstractCurl;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Class Curl
 * Create new order via curl
 */
class Curl extends AbstractCurl implements OrderInterface
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
        'store_id' => [
            'Default Store View' => 1
        ]
    ];

    /**
     * Steps for create order on backend
     *
     * @var array
     */
    protected $steps = [
        'customer_choice' => 'header',
        'products_choice' => 'search,items,shipping_method,totals,giftmessage,billing_method',
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
        $this->customer = $fixture->getDataFieldConfig('customer_id')['source']->getCustomerId();
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
        $result['order_data'] = $this->prepareOrderData($data);
        $result['shipping_data_address'] = $this->prepareShippingData($result['order_data']);
        $result['shipping_data_method_get'] = [
            'payment' => ['method' => 'checkmo'],
            'collect_shipping_rates' => 1
        ];
        $result['shipping_data_method_set'] = [
            'order' => ['shipping_method' => 'flatrate_flatrate'],
            'payment' => ['method' => 'checkmo']
        ];

        return $result;
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
            'payment' => ['method' => 'checkmo'],
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
        $result = [];
        foreach ($data['products'] as $key => $value) {
            $result['item'][$value->getId()]['qty'] = $data['data'][$key]['qty'];
            if (!$value->hasData('checkout_data')) {
                continue;
            }
            $checkoutData = $value->getCheckoutData();
            if ($checkoutData !== null) {
                preg_match('/CatalogProduct(.*)/', get_class($value), $matches);
                $result['item'][$value->getId()] += $this->{'prepare' . $matches[1] . 'Data'}($checkoutData, $value);
            }
        }
        return $result;
    }

    /**
     * Prepare data for configurable product
     *
     * @param array $checkoutData
     * @param FixtureInterface $product
     * @return array
     */
    protected function prepareConfigurableData(array $checkoutData, FixtureInterface $product)
    {
        $result = [];
        $attributesData = $product->hasData('configurable_attributes_data')
            ? $product->getConfigurableAttributesData()['attributes_data']
            : null;
        if ($attributesData == null) {
            return $result;
        }
        foreach ($checkoutData['configurable_options'] as $option) {
            $attributeIndex = str_replace('attribute_', '', $option['title']);
            $attributeId = $attributesData[$attributeIndex]['id'];
            $optionId = $attributesData[$attributeIndex]['options'][str_replace('option_', '', $option['value'])]['id'];
            $result['super_attribute'][$attributeId] = $optionId;
        }

        return $result;
    }

    /**
     * Prepare order data
     *
     * @param array $data
     * @return array
     */
    protected function prepareOrderData(array $data)
    {
        $result = [
            'name' => $this->customer->getFirstname(),
            'order' => [
                'currency' => $data['order_currency_code'],
                'account' => [
                    'group_id' => $this->customer->hasData('group_id') ? $this->customer->getGroupId() : 1,
                    'email' => $this->customer->getEmail()
                ],
                'shipping_method' => $data['shipping_method']
            ],
            'item' => $this->prepareOrderProductsData($data['entity_id']),
            'billing_address' => $this->prepareBillingAddress($data['billing_address_id']),
            'shipping_same_as_billing' => 'on',
            'payment' => ['method' => 'checkmo'],

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
        if ($this->order->getDataFieldConfig('store_id')['source']->store === null) {
            $storeId = $data['store_id'];
        } else {
            $storeId = $this->order->getDataFieldConfig('store_id')['source']->store->getStoreId();
            $this->steps['customer_choice'] .= ',data';
        }

        return [
            'currency_id' => $data['base_currency_code'],
            'customer_id' => $this->customer->getData('id'),
            'payment' => ['method' => 'free'],
            'store_id' => $storeId
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
        foreach ($data['products'] as $key => $value) {
            $result[$value->getId()] = [
                'qty' => $data['data'][$key]['qty'],
                'use_discount' => $data['data'][$key]['use_discount'],
                'action' => '',
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
