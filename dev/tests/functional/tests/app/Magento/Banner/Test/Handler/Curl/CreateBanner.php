<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Handler\Curl;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl;
use Mtf\System\Config;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Curl handler for creating a banner
 *
 */
class CreateBanner extends Curl
{
    /**
     * Post request for creating banner
     *
     * @param FixtureInterface $fixture [optional]
     * @throws \Exception
     * @return null|string banner_id
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $response = $this->postRequest($fixture);
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception('Banner creation by curl handler was not successful! Response: ' . $response);
        }
        $bannerId = null;
        if (preg_match('/\/banner\/edit\/id\/(\d+)/', $response, $matches)) {
            $bannerId = $matches[1];
        }
        return $bannerId;
    }

    /**
     * Post request for creating banner
     *
     * @param FixtureInterface $fixture [optional]
     * @return string
     */
    protected function postRequest(FixtureInterface $fixture = null)
    {
        $data = $fixture->getData('fields');
        $fields = [];
        foreach ($data as $key => $field) {
            $fields[$key] = $field['value'];
        }
        $url = $_ENV['app_backend_url'] . 'admin/banner/save/';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', [], $fields);
        $response = $curl->read();
        $curl->close();

        return $response;
    }
}
