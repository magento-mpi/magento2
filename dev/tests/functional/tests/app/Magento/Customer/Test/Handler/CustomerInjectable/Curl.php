<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Handler\CustomerInjectable;

use Mtf\System\Config;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\Fixture\FixtureInterface;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Class Curl
 * Curl handler for creating customer through registration page.
 */
class Curl extends AbstractCurl implements CustomerInjectableInterface
{
    /**
     * Post request for creating customer in frontend
     *
     * @param FixtureInterface|null $customer
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $customer = null)
    {
        /** @var CustomerInjectable $customer */
        $url = $_ENV['app_frontend_url'] . 'customer/account/createpost/?nocookie=true';
        $data = $customer->getData();
        $curl = new CurlTransport();
        unset($data['address']);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="global-messages-message-success"')) {
            throw new \Exception("Customer entity creating  by curl handler was not successful! Response: $response");
        }

        $this->setRewardOrStoreCreditPoints($customer);
        return ['id' => $this->getCustomerId($customer->getEmail())];
    }

    /**
     * Get customer id by email
     *
     * @param string $email
     * @return int|null
     */
    protected function getCustomerId($email)
    {
        $filter = ['email' => $email];
        $url = $_ENV['app_backend_url'] . 'customer/index/grid/filter/' . $this->encodeFilter($filter);
        $curl = new BackendDecorator(new CurlTransport(), new Config());

        $curl->write(CurlInterface::GET, $url, '1.0');
        $response = $curl->read();
        $curl->close();

        preg_match('/data-column="entity_id"[^>]*>\s*([0-9]+)\s*</', $response, $match);
        return empty($match[1]) ? null : $match[1];
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

    /**
     * Optional response if repository has reward points or store credit data
     *
     * @param CustomerInjectable $customer
     * @throws \Exception
     * @return void
     */
    protected function setRewardOrStoreCreditPoints(CustomerInjectable $customer)
    {
        $customerId = $this->getCustomerId($customer->getEmail());
        $data = $customer->getData();
        $curlData = [
            'rewardPoints' => [
                'url' => 'customer/index/save/active_tab/customer_edit_tab_reward/',
                'presentInDataSet' =>
                    [
                        'reward[points_delta]' => $customer->getRewardPointsDelta()
                    ],

            ],
            'storeCredit' => [
                'url' => 'customer/index/save/active_tab/customerbalance/',
                'presentInDataSet' =>
                    [
                        'customerbalance[amount_delta]' => $customer->getStoreCredit()
                    ],
            ],
        ];

        foreach ($curlData as $customerAction) {
            if (reset($customerAction['presentInDataSet']) !== null) {
                $url = $_ENV['app_backend_url'] . $customerAction['url'];
                $data['customer_id'] = $customerId;
                $data[key($customerAction['presentInDataSet'])] = reset($customerAction['presentInDataSet']);

                $curl = new BackendDecorator(new CurlTransport(), new Config);
                $curl->addOption(CURLOPT_HEADER, 1);
                $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
                $response = $curl->read();
                $curl->close();

                if (!strpos($response, 'data-ui-id="messages-message-success"')) {
                    throw new \Exception(
                        "Updating customer data by curl handler was not successful! Response: $response"
                    );
                }
            }
        }
    }
}
