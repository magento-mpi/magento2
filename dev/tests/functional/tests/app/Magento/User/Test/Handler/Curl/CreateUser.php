<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

class CreateUser extends Curl
{
    /**
     * @param array $fields
     * @return array
     */
    protected function _prepareData(array $fields)
    {
        $data = array();
        foreach ($fields as $key => $value) {
            $data[$key] = $value['value'];
        }
        return $data;
    }

    /**
     * Post request for creating user in backend
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */

    public function execute(Fixture $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . 'admin/user/save';
        $data = $this->_prepareData($fixture->getData('fields'));
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        return $response;
    }
}