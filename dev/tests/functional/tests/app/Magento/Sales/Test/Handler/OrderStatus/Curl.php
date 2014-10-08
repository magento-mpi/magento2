<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Handler\OrderStatus;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 * Curl handler for creating OrderStatus
 */
class Curl extends AbstractCurl implements OrderStatusInterface
{
    /**
     * Default attribute values for fixture
     *
     * @var array
     */
    protected $defaultAttributeValues = [
        'is_new' => 1,
        'store_labels[1]' => ''
    ];

    /**
     * Post request for creating OrderStatus
     *
     * @param FixtureInterface $fixture
     * @return void
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . 'sales/order_status/save/';
        $data = array_merge($this->defaultAttributeValues, $fixture->getData());
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("OrderStatus entity creating by curl handler was not successful! Response: $response");
        }
    }
}
