<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Handler\CmsBlock;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Magento\Backend\Test\Handler\Extractor;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 * Curl handler for creating CMS Block
 */
class Curl extends AbstractCurl implements CmsBlockInterface
{
    /**
     * Url for saving data
     *
     * @var string
     */
    protected $saveUrl = 'cms/block/save';

    /**
     * Mapping values for data
     *
     * @var array
     */
    protected $mappingData = [
        'store_id' => [
            'All Store Views' => 0,
        ],
        'is_active' => [
            'Enabled' => 1,
            'Disabled' => 0,
        ],
    ];

    /**
     * POST request for creating CMS Block
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
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("CMS Block entity creating  by curl handler was not successful!");
        }

        $url = 'cms/block/index/sort/creation_time/dir/desc';
        $regExpPattern = '@^.*block_id\/(\d+)\/.*' . $fixture->getTitle() . '@ms';
        $extractor = new Extractor($url, $regExpPattern);

        return ['block_id' => $extractor->getData()[1]];
    }

    /**
     * Prepare data from text to values
     *
     * @param $fixture
     * @return array
     */
    protected function prepareData($fixture)
    {
        $data = $this->replaceMappingData($fixture->getData());
        if (isset($data['store_id'])) {
            $data['stores'] = $data['store_id'];
        }

        return $data;
    }
}
