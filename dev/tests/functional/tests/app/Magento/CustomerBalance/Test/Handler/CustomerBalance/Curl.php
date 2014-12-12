<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerBalance\Test\Handler\CustomerBalance;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\System\Config;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Class Curl
 * Curl handler for creating customer balance through customer form backend
 */
class Curl extends AbstractCurl implements CustomerBalanceInterface
{
    /**
     * Post request for creating customer balance backend
     *
     * @param FixtureInterface|null $fixture
     * @return void
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . 'customer/index/save/active_tab/customerbalance/';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception(
                "Adding customer balance by curl handler was not successful! Response: $response"
            );
        }
    }

    /**
     * Prepare data from text to values
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData($fixture)
    {
        /** @var \Magento\CustomerBalance\Test\Fixture\CustomerBalance $fixture */
        $customer = $fixture->getDataFieldConfig('customer_id')['source']->getCustomer();
        /** @var \Magento\Store\Test\Fixture\Website $website */
        $website = $fixture->getDataFieldConfig('website_id')['source']->getWebsite();
        /** @var \Magento\Customer\Test\Fixture\CustomerInjectable $customer */
        $data['customer_id'] = $customer->getId();
        $data['customerbalance']['amount_delta'] = $fixture->getBalanceDelta();
        $data['customerbalance']['website_id'] = $website->getWebsiteId();
        $data['customerbalance']['comment'] = $fixture->getAdditionalInfo();

        return $data;
    }
}
