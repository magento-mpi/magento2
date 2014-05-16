<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sitemap\Test\Handler\Sitemap; 

use Magento\Sitemap\Test\Handler\Sitemap;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 *
 * @package Magento\Sitemap\Test\Handler\Sitemap
 */
class Curl extends AbstractCurl implements SitemapInterface
{
    /**
     * Default attribute values for fixture
     *
     * @var array
     */
    protected $defaultAttributeValues = ['store_id' => 1];

    /**
     * Prepare data for deleting sitemap
     *
     * @param FixtureInterface $fixture
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . 'admin/sitemap/save/';
        $data = array_merge($this->defaultAttributeValues, $fixture->getData());
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Sitemap entity creating by curl handler was not successful! Response: $response");
        }

        return ['sitemap_id' => $this->getSitemapId($data)];
    }

    /**
     * Get id after created sitemap
     *
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    protected function getSitemapId(array $data)
    {
        //Sort data in grid to define sitemap id if more than 20 items in grid
        $url = $_ENV['app_backend_url'] . 'admin/sitemap/index/sort/sitemap_id/dir/desc';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0');
        $response = $curl->read();
        $curl->close();

        $pattern = '/class=\" col\-id col\-sitemap_id\W*>\W+(\d+)\W+<\/td>\W+<td[\w\s\"=\-]*?>\W+?'
            . $data['sitemap_filename'] . '/siu';
        preg_match($pattern, $response, $matches);
        if (empty($matches)) {
            throw new \Exception('Cannot find sitemap id');
        }
        return $matches[1];
    }
}
