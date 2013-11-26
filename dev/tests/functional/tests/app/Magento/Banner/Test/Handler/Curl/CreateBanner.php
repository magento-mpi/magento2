<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Curl handler for creating a banner
 *
 * @package Magento\Banner\Test\Handler\Curl
 */
class CreateBanner extends Curl
{
    /**
     * Post request for creating banner
     *
     * @param Fixture $fixture [optional]
     * @throws \Exception
     * @return null|string banner_id
     */
    public function execute(Fixture $fixture = null)
    {
        $data = $fixture->getData('fields');
        $fields = array();
        foreach ($data as $key => $field) {
            $fields[$key] = $field['value'];
        }
        $url = $_ENV['app_backend_url'] . 'admin/banner/save/active_tab/content_section/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $fields);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'You saved the banner')) {
            throw new \Exception("Banner creation by curl handler was not successful! Response: $response");
        }
        $id = null;
        if (preg_match('/\/banner\/edit\/id\/(\d+)/', $response, $matches)) {
            $id = $matches[1];
        }
        return $id;
    }
}