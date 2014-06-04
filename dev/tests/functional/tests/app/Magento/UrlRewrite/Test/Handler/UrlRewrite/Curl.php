<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Handler\UrlRewrite; 

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class Curl
 * Create url rewrite
 */
class Curl extends AbstractCurl implements UrlRewriteInterface
{
    /**
     * Data mapping
     *
     * @var array
     */
    protected $dataMapping = [
        'store_id' => ['Default Store View' => 1],
        'options' => [
            'Temporary (302)' => 'R',
            'Temporary (301)' => 'RP',
            'No' => ''
        ]
    ];

    /**
     * Url for save rewrite
     *
     * @var string
     */
    protected $url = 'admin/urlrewrite/save/';

    /**
     * Post request for creating url rewrite
     *
     * @param FixtureInterface $fixture
     * @throws \Exception
     * @return mixed|void
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . $this->url . $fixture->getData('id_path');
        $data = $this->prepareData($fixture->getData());
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Product creation by curl handler was not successful! Response: $response");
        }
        $curl->close();
    }

    /**
     * Prepare data
     *
     * @param array $data
     * @return array
     */
    protected function prepareData(array $data)
    {
        foreach ($data as $key => $value) {
            if (isset($this->dataMapping[$key])) {
                $data[$key] = $this->dataMapping[$key][$value];
            }
        }
        return $data;
    }
}
