<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlInterface;

/**
 * cURL handler for integration creation.
 */
class CreateIntegration extends Curl
{
    /**
     * Create integration using cURL client.
     *
     * @param Fixture $fixture
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        /** Prepare data for the post to integration save URL */
        $data = $fixture->getData('fields');
        $fields = array();
        foreach ($data as $key => $field) {
            $fields[$key] = $field['value'];
        }
        /** Initialize cURL client which is authenticated to the Magento backend */
        $curl = new \Mtf\Util\Protocol\CurlTransport\BackendDecorator(new CurlTransport(), new \Mtf\System\Config());
        /** Create new integration via cURL */
        $url = $_ENV['app_backend_url'] . 'admin/integration/save';
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $fields);
        $response = $curl->read();
        $curl->close();
        return $response;
    }
}
