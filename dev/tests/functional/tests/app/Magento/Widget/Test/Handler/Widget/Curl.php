<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Handler\Widget;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;
use Magento\Backend\Test\Handler\Extractor;

/**
 * Curl handler for creating widgetInstance/frontendApp
 */
class Curl extends AbstractCurl
{
    /**
     * Mapping values for Store Views
     *
     * @var array
     */
    protected $storeIds = [
        'All Store Views' => 0
    ];

    /**
     * Post request for creating widget instance
     *
     * @param FixtureInterface $fixture [optional]
     * @throws \Exception
     * @return null|array instance id
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . 'admin/widget_instance/save/code/' . $fixture->getData('code') .
            '/theme_id/' . $fixture->getData('theme_id');
        unset($data['code']);
        unset($data['theme_id']);
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Widget instance creation by curl handler was not successful! Response: $response");
        }

        $url = 'admin/widget_instance/index/sort/instance_id/dir/desc';
        $regExpPattern = '@^.*instance_id\/(\d+)\/.*' . $fixture->getTitle() . '@ms';
        $extractor = new Extractor($url, $regExpPattern);

        return ['id' => $extractor->getData()[1]];
    }

    /**
     * Prepare data from text to values
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData($fixture)
    {
        $data = $this->replaceMappingData($fixture->getData());
        if (isset($data['store_ids'])) {
            $storeIds = [];
            foreach ($data['store_ids'] as $storeId) {
                $storeIds[] = isset($this->storeIds[$storeId]) ? $this->storeIds[$storeId] : $storeId;
            }
            $data['store_ids'] = $storeIds;
        }

        return $data;
    }
}
