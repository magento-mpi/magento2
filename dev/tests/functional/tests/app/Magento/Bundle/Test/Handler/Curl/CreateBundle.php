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

namespace Magento\Bundle\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class CreateBundle.
 * Curl handler for creating bundle product.
 *
 * @package Magento\Bundle\Test\Handler\Curl
 */
class CreateBundle extends Curl
{

    protected function _prepareData($params, $prefix = null)
    {
        $data = array();
        foreach ($params as $key => $values) {
            if ($key == 'bundle_selections') {
                $data = array_merge($data, $this->getBundleData($values['value']));
            } else {
                $value = isset($values['input_value']) ? $values['input_value'] : $values['value'];
                if ($prefix) {
                    $data[$prefix][$key] = $value;
                } else {
                    $data[$key] = $value;
                }
            }
        }
        return $data;
    }

    protected function getBundleData($params)
    {
        $data = array();
        foreach ($params as $options) {
            if (isset($options['assigned_products'])) {
                $data['bundle_selections'][] = $this->getSelections($options['assigned_products']);
                unset($options['assigned_products']);
            }
            $data['bundle_options'][] = $this->_prepareData($options);
        }
        return $data;
    }

    protected function getSelections($products)
    {
        $data = array();
        foreach ($products as $product) {
            $product = isset($product['data']) ? $product['data'] : array();
            $data[] = $this->_prepareData($product);
        }
        return $data;
    }

    protected function getOptions($options)
    {
        $data = array();
        foreach ($options as $option) {
            $data[] = $this->_prepareData($option);
        }
        return $data;
    }

    /**
     * Post request for creating bundle product
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        $config = $fixture->getDataConfig();

        $requestParams = isset($config['create_url_params']) ? $config['create_url_params'] : array();
        $params = '';
        foreach ($requestParams as $key => $value) {
            $params .= $key . '/' . $value . '/';
        }

        $prefix = isset($config['input_prefix']) ? $config['input_prefix'] : null;
        $data = $this->_prepareData($fixture->getData('fields'), $prefix);

        $url = $_ENV['app_backend_url'] . 'admin/catalog_product/save/' . $params;

        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        return $response;
    }
}
