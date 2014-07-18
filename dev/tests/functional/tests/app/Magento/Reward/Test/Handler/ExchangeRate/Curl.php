<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Handler\ExchangeRate;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 */
class Curl extends AbstractCurl implements ExchangeRateInterface
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

        // TODO Get Exchange Rate Id
        preg_match("~Location: [^\\s]*giftwrapping\\/edit\\/id\\/(\\d+)~", $response, $matches);
        // TODO Get Exchange Rate Id

        $id = isset($matches[1]) ? $matches[1] : null;
        return ['rate_id' => $id];
    }
}
