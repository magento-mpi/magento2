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
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("CMS Block entity creating  by curl handler was not successful!");
        }

        return ['block_id' => $this->getBlockId($fixture->getTitle())];
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

    /**
     * Get Block id by title
     *
     * @param string $title
     * @return null/int
     */
    protected function getBlockId($title)
    {
        // Sort data in grid to define CMS block id if more than 20 items in grid
        $url = $_ENV['app_backend_url'] . 'cms/block/index/sort/title/dir/asc';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::GET, $url, '1.0');
        $response = $curl->read();
        $curl->close();
        $pattern = '/.*\/block_id\/(\d+).*' . $title . '/siu';
        preg_match($pattern, $response, $match);

        return empty($match[1]) ? null : $match[1];
    }
}
