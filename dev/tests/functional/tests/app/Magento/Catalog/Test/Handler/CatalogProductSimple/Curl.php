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

namespace Magento\Catalog\Test\Handler\CatalogProductSimple;

use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Class CreateProduct
 */
class Curl extends AbstractCurl implements CatalogProductSimpleInterface
{
    /**
     * Post request for creating simple product
     *
     * @param FixtureInterface $fixture [optional]
     * @return mixed|string
     * @throws \Exception
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $config = $fixture->getDataConfig();
        $prefix = isset($config['input_prefix']) ? $config['input_prefix'] : null;
        // @todo remove "if" when fixtures refactored
        if ($fixture instanceof InjectableFixture) {
            $fields = $fixture->getData();
            if ($prefix) {
                $data[$prefix] = $fields;
            } else {
                $data = $fields;
            }
        } else {
            $data = $this->_prepareData($fixture->getData('fields'), $prefix);
        }

        if ($fixture->getData('category_id')) {
            $data['product']['category_ids'] = $fixture->getData('category_id');
        }
        $url = $this->_getUrl($config);
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        if (!strpos($response, 'data-ui-id="messages-message-success"')) {
            throw new \Exception("Product creation by curl handler was not successful! Response: $response");
        }
        preg_match("~Location: [^\s]*\/id\/(\d+)~", $response, $matches);
        $id = isset($matches[1]) ? $matches[1] : null;
        return ['id' => $id];
    }

    /**
     * Prepare POST data for creating product request
     *
     * @param array $params
     * @param string|null $prefix
     * @return array
     */
    protected function _prepareData($params, $prefix = null)
    {
        $data = array();
        foreach ($params as $key => $values) {
            $value = $this->_getValue($values);
            //do not add this data if value does not exist
            if (null === $value) {
                continue;
            }
            if (isset($values['input_name'])) {
                $data[$values['input_name']] = $value;
            } elseif ($prefix) {
                $data[$prefix][$key] = $value;
            } else {
                $data[$key] = $value;
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
     * Retrieve URL for request with all necessary parameters
     *
     * @param array $config
     * @return string
     */
    protected function _getUrl(array $config)
    {
        $requestParams = isset($config['create_url_params']) ? $config['create_url_params'] : array();
        $params = '';
        foreach ($requestParams as $key => $value) {
            $params .= $key . '/' . $value . '/';
        }
        return $_ENV['app_backend_url'] . 'catalog/product/save/' . $params . 'popup/1/';
    }
}
