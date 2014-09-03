<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\Handler\Store;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 * Curl handler for creating Store view.
 */
class Curl extends AbstractCurl implements StoreInterface
{
    /**
     * Url for saving data
     *
     * @var string
     */
    protected $saveUrl = 'admin/system_store/save';

    /**
     * Mapping values for data
     *
     * @var array
     */
    protected $mappingData = [
        'group_id' => [
            'Main Website Store' => 1
        ],
        'is_active' => [
            'Enabled' => 1,
            'Disabled' => 0
        ],
    ];

    /**
     * POST request for creating store
     *
     * @param FixtureInterface|null $fixture [optional]
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . $this->saveUrl;
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Store View entity creating  by curl handler was not successful! Response: $response");
        }

        return ['store_id' => $this->getStoreId($fixture->getName())];
    }

    /**
     * Prepare data from text to values
     *
     * @param $fixture
     * @return array
     */
    protected function prepareData($fixture)
    {
        $data['store'] = $this->replaceMappingData($fixture->getData());
        $data['store_action'] = isset($data['store_action']) ? $data['store_action'] : 'add';
        $data['store_type'] = isset($data['store_type']) ? $data['store_type'] : 'store';

        return $data;
    }

    /**
     * Get Store id by name after creating Store
     *
     * @param string $name
     * @return int|null
     * @throws \Exception
     */
    protected function getStoreId($name)
    {
        //Set pager limit to 2000 in order to find created store view by name
        $url = $_ENV['app_backend_url'] . 'admin/system_store/index/sort/store_title/dir/asc/limit/2000';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0');
        $response = $curl->read();

        $expectedUrl = '/admin/system_store/editStore/store_id/';
        $expectedUrl = preg_quote($expectedUrl);
        $expectedUrl = str_replace('/', '\/', $expectedUrl);
        preg_match('/' . $expectedUrl . '([0-9]*)\/(.)*>' . $name . '<\/a>/', $response, $matches);

        if (empty($matches)) {
            throw new \Exception('Cannot find store id');
        }

        return empty($matches[1]) ? null : $matches[1];
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
