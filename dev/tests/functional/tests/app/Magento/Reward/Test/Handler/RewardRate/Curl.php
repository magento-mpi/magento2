<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Handler\RewardRate;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 * Curl creation of reward points exchange rate
 */
class Curl extends AbstractCurl implements RewardRateInterface
{
    /**
     * Mapping for reward rate exchange data
     *
     * @var array
     */
    protected $mappingData = [
        'direction' => [
            'Points to Currency' => 1,
            'Currency to Points' => 2,
        ],
    ];

    /**
     * Post request for creating rate exchange
     *
     * @param FixtureInterface $fixture
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        /** @var \Magento\Reward\Test\Fixture\RewardRate $fixture */
        $data['rate'] = $this->replaceMappingData($fixture->getData());
        $data['rate']['customer_group_id'] = $fixture->getDataFieldConfig('customer_group_id')['source']
            ->getCustomerGroup()
            ->getCustomerGroupId();
        $data['rate']['website_id'] = $fixture->getDataFieldConfig('website_id')['source']
            ->getWebsite()
            ->getWebsiteId();

        $url = $_ENV['app_backend_url'] . 'admin/reward_rate/save/';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Exchange Rate creation by curl handler was not successful! Response: $response");
        }

        return ['rate_id' => $this->getRateId()];
    }

    /**
     * Get Reward exchange rate id
     *
     * @return string|null
     */
    protected function getRateId()
    {
        $url = $_ENV['app_backend_url'] . 'admin/reward_rate/index/sort/rate_id/dir/desc/';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::GET, $url, '1.0');
        $response = $curl->read();
        $curl->close();

        preg_match('/data-column="rate_id"[^>]*>\s*([0-9]+)\s*</', $response, $match);
        return empty($match[1]) ? null : $match[1];
    }
}
