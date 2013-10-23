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
 * Class Create Configurable Product
 */
class CreateConfigurable extends Curl
{
    /**
     * Returns transformed data
     *
     * @param Fixture $fixture
     * @return mixed
     */
    protected function _prepareData(Fixture $fixture)
    {
        $params = $fixture->getData('fields');


        foreach ($params as $key => $value) {
            if (array_key_exists($key, $this->_substitution) && array_key_exists($value, $this->_substitution[$key])) {
                $params[$key] = $this->_substitution[$key][$value];
            }
        }
        return $params;
    }

    /**
     * Create configurable product
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        $url = $_ENV['app_backend_url']
            . 'admin/catalog_product/save/'
            . $fixture->getUrlParams('create_url_params');
        $params = $this->_prepareData($fixture);
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $params);
        $response = $curl->read();
        $curl->close();
        return $response;
    }
}
