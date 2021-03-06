<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\Handler\OrderStatus;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\System\Config;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

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
        'store_labels[1]' => '',
    ];

    /**
     * Mapping values for data
     *
     * @var array
     */
    protected $mappingData = [
        'state' => [
            'Pending' => 'new',
        ],
        'is_default' => [
            'Yes' => 1,
            'No' => 0,
        ],
        'visible_on_front' => [
            'Yes' => 1,
            'No' => 0,
        ],
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
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.1', [], $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("OrderStatus entity creating by curl handler was not successful! Response: $response");
        }

        if (isset($data['state'])) {
            $url = $_ENV['app_backend_url'] . 'sales/order_status/assignPost/';
            $data = $this->replaceMappingData($data);
            $curl = new BackendDecorator(new CurlTransport(), new Config());
            $curl->write(CurlInterface::POST, $url, '1.1', [], $data);
            $response = $curl->read();
            $curl->close();

            if (!strpos($response, 'data-ui-id="messages-message-success"')) {
                throw new \Exception(
                    "Assigning OrderStatus entity by curl handler was not successful! Response: $response"
                );
            }
        }
    }
}
