<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Handler\GiftRegistryType;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Magento\Backend\Test\Handler\Extractor;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 * Curl handler for creating Gift Registry Type
 */
class Curl extends AbstractCurl implements GiftRegistryTypeInterface
{
    /**
     * Url for saving data
     *
     * @var string
     */
    protected $saveUrl = 'admin/giftregistry/save/store/0/back/edit/active_tab/general_section/';

    /**
     * Mapping values for data
     *
     * @var array
     */
    protected $mappingData = [
        'is_listed' => [
            'Yes' => 1,
            'No' => 0,
        ],
    ];

    /**
     * POST request for creating gift registry type
     *
     * @param FixtureInterface|null $fixture [optional]
     * @throws \Exception
     * @return array
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . $this->saveUrl;
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Gift registry type creating by curl handler was not successful! Response: $response");
        }

        $url = 'admin/giftregistry/index/sort/type_id/dir/desc';
        $regExpPattern = '@^.*id\/(\d+)\/.*' . $fixture->getCode() . '@ms';
        $extractor = new Extractor($url, $regExpPattern);
        return ['type_id' => $extractor->getData()[1]];
    }

    /**
     * Prepare data for CURL request
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData($fixture)
    {
        $data = $this->replaceMappingData($fixture->getData());
        foreach ($data as $key => $value) {
            unset ($data[$key]);
            if ($key != 'attributes') {
                $data['type['. $key .']'] = $value;
            }
        }
        return $data;
    }
}
