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

/**
 * Curl handler for persisting Magento user
 *
 * @package Magento\Core\Test\Handler\Curl
 */
class CreateUser extends Curl
{
    /**
     * Prepare data for using in the execute method
     *
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
     * Get id for newly created user
     *
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    protected function _getUserId($data)
    {
        //Sort data in grid to define user id if more than 20 items in grid
        $url = $_ENV['app_backend_url'] . 'admin/user/roleGrid/sort/user_id/dir/desc';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0');
        $response = $curl->read();
        $curl->close();
        preg_match('/class=\"a\-right col\-user_id\W*>\W+(\d+)\W+<\/td>\W+<td[\w\s\"=\-]*?>\W+?'
        . $data['username'] . '/siu', $response, $matches);
        if (empty($matches)) {
            throw new \Exception('Cannot find user id');
        }
        return $matches[1];
    }

    /**
     * Post request for creating user in backend
     *
     * @param Fixture $fixture
     * @return array|mixed
     * @throws \Exception
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
        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("User creation by curl handler was not successful! Response: $response");
            //Sort data in grid to define user id if more than 20 items in grid
            $url = $_ENV['app_backend_url'] . 'admin/user/roleGrid/sort/user_id/dir/desc';
            $curl = new BackendDecorator(new CurlTransport(), new Config);
            $curl->addOption(CURLOPT_HEADER, 1);
            $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
            $response = $curl->read();
            $curl->close();
            $data['id'] = $this->_getUserId($data);
            return $data;
        }
    }
}

