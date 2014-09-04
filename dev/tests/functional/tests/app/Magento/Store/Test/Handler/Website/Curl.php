<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\Handler\Website;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 * Curl handler for creating Website.
 */
class Curl extends AbstractCurl implements WebsiteInterface
{
    /**
     * POST request for creating Website
     *
     * @param FixtureInterface $fixture
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->prepareData($fixture);
        $url = $_ENV['app_backend_url'] . 'admin/system_store/save/';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Website entity creating by curl handler was not successful! Response: $response");
        }

        return ['website_id' => $this->getWebSiteIdByWebsiteName($fixture->getName())];
    }

    /**
     * Get website id by website name
     *
     * @param string $websiteName
     * @return int
     * @throws \Exception
     */
    protected function getWebSiteIdByWebsiteName($websiteName)
    {
        //Set pager limit to 2000 in order to find created website by name
        $url = $_ENV['app_backend_url'] . 'admin/system_store/index/sort/group_title/dir/asc/limit/2000';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0');
        $response = $curl->read();

        $expectedUrl = '/admin/system_store/editWebsite/website_id/';
        $expectedUrl = preg_quote($expectedUrl);
        $expectedUrl = str_replace('/', '\/', $expectedUrl);
        preg_match('/' . $expectedUrl . '([0-9]*)\/(.)*>' . $websiteName . '<\/a>/', $response, $matches);

        if (empty($matches)) {
            throw new \Exception('Cannot find website id');
        }

        return intval($matches[1]);
    }

    /**
     * Prepare data from text to values
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture)
    {
        $data['website']= $fixture->getData();
        $data['store_action'] = isset($data['store_action']) ? $data['store_action'] : 'add';
        $data['store_type'] = isset($data['store_type']) ? $data['store_type'] : 'website';

        return $data;
    }
}
