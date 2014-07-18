<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Handler\Reward;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 */
class Curl extends AbstractCurl implements RewardInterface
{
    /**
     * Mapping for reward rate exchange data
     *
     * @var array
     */
    protected $mappingData = [
        'website_id' => [
            'All Websites' => 0,
            'Main Website' => 1,
        ],
        'customer_group_id' => [
            'All Customer Groups' => 0,
            'General' => 1,
            'Wholesale' => 2,
            'Retailer' => 3,
        ],
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
        $data['rate'] = $this->replaceMappingData($fixture->getData());

        $url = $_ENV['app_backend_url'] . 'admin/reward_rate/save/';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Exchange Rate creation by curl handler was not successful! Response: $response");
        }

        return ['rate_id' => $this->getRateId($response)];
    }

    /**
     * Get Reward exchange rate id
     *
     * @param $response
     * @return string|null
     */
    protected function getRateId($response)
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
