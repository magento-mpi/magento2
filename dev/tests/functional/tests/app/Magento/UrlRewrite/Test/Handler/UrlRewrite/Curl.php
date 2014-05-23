<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Handler\UrlRewrite; 

use Magento\UrlRewrite\Test\Handler\UrlRewrite\UrlRewriteInterface;
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
        'Default Store View' => 1,
        'Temporary (302)' => 'R',
        'Temporary (301)' => 'RP',
        'No' => ''
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
     * @return mixed|void
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . $this->url . $fixture->getData('rewrite_path');
        $data = $this->prepareData($fixture->getData());
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $curl->read();
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
        array_walk_recursive(
            $data,
            function (&$value, $key, $placeholder) {
                $value = isset($placeholder[$value]) ? $placeholder[$value] : $value;
            },
            $this->dataMapping
        );
        return $data;
    }
}
