<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Handler\Reward;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 * Curl creation of reward points
 */
class Curl extends AbstractCurl implements RewardInterface
{
    /**
     * Post request for creating reward points
     *
     * @param FixtureInterface $fixture
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        /** @var \Magento\Reward\Test\Fixture\Reward $fixture */
        $customer = $fixture->getDataFieldConfig('customer_id')['source']->getCustomer();
        /** @var CustomerInjectable $customer */
        $data = $customer->getData();
        $data['customer_id'] = $customer->getId();
        $data['reward']['points_delta'] = $fixture->getPointsDelta();

        $url = $_ENV['app_backend_url'] . 'customer/index/save/active_tab/customer_edit_tab_reward/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception(
                "Adding reward points by curl handler was not successful! Response: $response"
            );
        }
    }
}
