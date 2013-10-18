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

namespace Magento\Catalog\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class CreateCategory.
 * Curl handler for creating category.
 *
 * @package Magento\Catalog\Test\Handler\Curl
 */
class CreateCategory extends Curl
{
    /**
     * Post request for creating category
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        $config = $fixture->getDataConfig();

        $requestParams = isset($config['request_params']) ? $config['request_params'] : array();
        $params = '';
        foreach ($requestParams as $key => $value) {
            $params .= $key . '/' . $value . '/';
        }

        $data = $fixture->getData('fields');
        $fields = array();
        foreach ($data as $key => $field) {
            $fields[$key] = $field['value'];
        }

        $url = $_ENV['app_backend_url'] . 'admin/catalog_category/save/' . $params;

        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $fields);
        $response = $curl->read();
        $curl->close();

        return $response;
    }
}
 