<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Handler\CustomerInjectable;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 * Curl handler for creating customer through registration page.
 */
class Curl extends AbstractCurl implements CustomerInjectableInterface
{
    /**
     * Default customer group
     */
    const GENERAL_GROUP = 'General';

    /**
     * Mapping values for data
     *
     * @var array
     */
    protected $mappingData = [
        'group_id' => [
            self::GENERAL_GROUP => 1
        ],
        'country_id' => [
            'United States' => 'US'
        ],
        'region_id' => [
            'California' => 12
        ]
    ];

    /**
     * Curl mapping data
     *
     * @var array
     */
    protected $curlMapping = [
        'group_id' => [
            'prefix' => 'account'
        ],
        'firstname' => [
            'prefix' => 'account'
        ],
        'lastname' => [
            'prefix' => 'account'
        ],
        'email' => [
            'prefix' => 'account'
        ],
        'dob' => [
            'prefix' => 'account'
        ],
        'taxvat' => [
            'prefix' => 'account'
        ],
        'gender' => [
            'prefix' => 'account'
        ]
    ];

    /**
     * Post request for creating customer in frontend
     *
     * @param FixtureInterface|null $customer
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $customer = null)
    {
        $address = [];
        $result = [];
        /** @var CustomerInjectable $customer */
        $url = $_ENV['app_frontend_url'] . 'customer/account/createpost/?nocookie=true';
        $data = $customer->getData();

        if ($customer->hasData('address')) {
            $address = $customer->getAddress();
            unset($data['address']);
        }

        $curl = new CurlTransport();
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="global-messages-message-success"')) {
            throw new \Exception("Customer entity creating  by curl handler was not successful! Response: $response");
        }

        $result['id'] = $this->getCustomerId($customer->getEmail());
        $data['customer_id'] = $result['id'];

        if (!empty($address)) {
            $data['address'] = $address;
            $this->addAddress($data);
        }

        return $result;
    }

    /**
     * Get customer id by email
     *
     * @param string $email
     * @return int|null
     */
    protected function getCustomerId($email)
    {
        $url = $_ENV['app_backend_url'] . 'customer/index/grid/filter/' . $this->encodeFilter(['email' => $email]);
        $curl = new BackendDecorator(new CurlTransport(), new Config());

        $curl->write(CurlInterface::GET, $url, '1.0');
        $response = $curl->read();
        $curl->close();

        preg_match('/data-column="entity_id"[^>]*>\s*([0-9]+)\s*</', $response, $match);
        return empty($match[1]) ? null : $match[1];
    }

    /**
     * Add addresses in to customer account
     *
     * @param array $data
     * @return void
     * @throws \Exception
     */
    protected function addAddress(array $data)
    {
        $curlData = [];
        $url = $_ENV['app_backend_url'] . 'customer/index/save';
        foreach ($data as $key => $value) {
            if (isset($this->curlMapping[$key]['prefix'])) {
                $curlData[$this->curlMapping[$key]['prefix']][$key] = $value;
                unset($data[$key]);
            }
        }
        unset($data['password'], $data['password_confirmation']);
        $curlData['account']['group_id'] = isset($curlData['account']['group_id'])
            ? $curlData['account']['group_id']
            : self::GENERAL_GROUP;

        $curlData = $this->replaceMappingData(array_merge($curlData, $data));
        foreach (array_keys($curlData['address']) as $key) {
            $curlData['address'][$key]['_deleted'] = '';
            $curlData['address'][$key]['region'] = '';
            if (!is_array($curlData['address'][$key]['street'])) {
                $street = $curlData['address'][$key]['street'];
                $curlData['address'][$key]['street'] = [];
                $curlData['address'][$key]['street'][] = $street;
            }
            $newKey = '_item' . ($key + 1);
            if ($curlData['address'][$key]['default_billing'] === 'Yes') {
                unset($curlData['address'][$key]['default_billing']);
                $curlData['account']['default_billing'] = $newKey;
            }
            if ($curlData['address'][$key]['default_shipping'] === 'Yes') {
                unset($curlData['address'][$key]['default_shipping']);
                $curlData['account']['default_shipping'] = $newKey;
            }
            $curlData['address'][$newKey] = $curlData['address'][$key];
            unset($curlData['address'][$key]);
        }

        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $curlData);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception('Failed to assign an address to the customer!');
        }
    }

    /**
     * Encoded filter parameters
     *
     * @param array $filter
     * @return string
     */
    protected function encodeFilter(array $filter)
    {
        $result = [];
        foreach ($filter as $name => $value) {
            $result[] = "{$name}={$value}";
        }
        $result = implode('&', $result);

        return base64_encode($result);
    }
}
