<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Handler\CmsPage;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 * Curl handler for creating cms page
 */
class Curl extends AbstractCurl implements CmsPageInterface
{
    /**
     * Data mapping
     *
     * @var array
     */
    protected $mappingData = [
        'status' => ['Published' => 1, 'Disabled' => 0],
        'store_id' => ['All Store Views' => 0],
    ];

    /**
     * Url for save rewrite
     *
     * @var string
     */
    protected $url = 'admin/cms_page/save/back/edit/active_tab/content_section/';

    /**
     * Post request for creating cms page
     *
     * @param FixtureInterface $fixture
     * @return array
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . $this->url;
        $data = $this->replaceMappingData($fixture->getData());
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Page creation by curl handler was not successful! Response: $response");
        }

        preg_match("~page_id\/(\d*?)\/~", $response, $matches);
        $id = isset($matches[1]) ? $matches[1] : null;
        return ['page_id' => $id];
    }
}
