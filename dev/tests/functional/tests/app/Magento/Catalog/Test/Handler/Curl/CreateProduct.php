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
use Mtf\Util\Protocol\CurlTransport;

/**
 * Class CreateProduct
 *
 * @package Magento\Catalog\Test\Handler\Curl
 */
class CreateProduct extends Curl
{
    /**
     * Create product
     *
     * @param Fixture $fixture [optional]
     * @return int
     */
    public function execute(Fixture $fixture = null)
    {
        $config = $fixture->getDataConfig();

        $requestParams = isset($config['create_url_params']) ? $config['create_url_params'] : array();
        $params = '';
        foreach ($requestParams as $key => $value) {
            $params .= $key . '/' . $value . '/';
        }
        $data = $fixture->getData('fields');
        $fields = array();
        foreach ($data as $key => $field) {
            $fields['product'][$key] = $field['value'];
        }

        $url = $_ENV['app_backend_url'] . 'admin/catalog_product/save/' . $params;

        $curl = new CurlTransport();
        $curl->write(CurlTransport::POST, $url, '1.0', array(), $fields);
        $response = $curl->read();
        $curl->close();

        return $response;
    }
}
