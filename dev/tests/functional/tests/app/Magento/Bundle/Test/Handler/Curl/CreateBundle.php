<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Handler\Curl;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class CreateBundle
 * Curl handler for creating bundle product.
 */
class CreateBundle extends Curl
{
    /**
     * Prepare POST data for creating bundle product request
     *
     * @param array $params
     * @param string|null $prefix
     * @return array
     */
    protected function _prepareData($params, $prefix = null)
    {
        $data = [];
        foreach ($params as $key => $values) {
            if ($key == 'bundle_selections') {
                $data = array_merge($data, $this->_getBundleData($values['value']));
            } else {
                $value = $this->_getValue($values);
                //do not add this data if value does not exist
                if (null === $value) {
                    continue;
                }
                if (isset($values['input_name'])) {
                    $key = $values['input_name'];
                }
                if ($prefix) {
                    $data[$prefix][$key] = $value;
                } else {
                    $data[$key] = $value;
                }
            }
        }
        return $data;
    }

    /**
     * Retrieve field value or return null if value does not exist
     *
     * @param array $values
     * @return null|mixed
     */
    protected function _getValue($values)
    {
        if (!isset($values['value'])) {
            return null;
        }
        return isset($values['input_value']) ? $values['input_value'] : $values['value'];
    }

    /**
     * Prepare bundle specific data
     *
     * @param array $params
     * @return array
     */
    protected function _getBundleData($params)
    {
        $data = [];
        foreach ($params['bundle_options'] as $options) {
            if (isset($options['assigned_products'])) {
                $data['bundle_selections'][] = $this->_getSelections($options['assigned_products']);
                unset($options['assigned_products']);
            }
            $data['bundle_options'][] = $this->_prepareData($options) + ['delete' => ''];
        }
        return $data;
    }

    /**
     * Retrieve URL for request with all necessary parameters
     *
     * @param array $config
     * @return string
     */
    protected function _getUrl(array $config)
    {
        $requestParams = isset($config['create_url_params']) ? $config['create_url_params'] : [];
        $params = '';
        foreach ($requestParams as $key => $value) {
            $params .= $key . '/' . $value . '/';
        }
        return $_ENV['app_backend_url'] . 'catalog/product/save/' . $params . 'popup/1/back/edit';
    }

    /**
     * Prepare product selections data
     *
     * @param array $products
     * @return array
     */
    protected function _getSelections($products)
    {
        $data = [];
        foreach ($products as $product) {
            $product = isset($product['data']) ? $product['data'] : [];
            $data[] = $this->_prepareData($product) + ['delete' => ''];
        }
        return $data;
    }

    /**
     * Post request for creating bundle product
     *
     * @param FixtureInterface $fixture [optional]
     * @return mixed|string
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $config = $fixture->getDataConfig();

        $prefix = isset($config['input_prefix']) ? $config['input_prefix'] : null;
        $data = $this->_prepareData($fixture->getData('fields'), $prefix);
        if ($fixture->getData('category_id')) {
            $data['product']['category_ids'] = $fixture->getData('category_id');
        }
        $url = $this->_getUrl($config);
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Product creation by curl handler was not successful! Response: $response");
        }
        preg_match("~Location: [^\s]*\/id\/(\d+)~", $response, $matches);
        return isset($matches[1]) ? $matches[1] : null;
    }
}
