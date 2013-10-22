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
     * Returns transformed data
     *
     * @param Fixture $fixture
     * @return mixed
     */
    protected function _prepareData(Fixture $fixture)
    {
        $defaultsCounter = 0;
        $params = array();
        $fixtureData = $fixture->getData();
        $configParams = $fixture->getDataConfig();

        $params['general[store]'] = $configParams['request_params']['store'];
        foreach ($fixtureData['fields'] as $key => $field) {
            if (isset($field['use_default'])) {
                $params['use_config[' . $defaultsCounter. ']'] = $key;
                ++$defaultsCounter;
            } else {
                $params[isset($configParams['input_prefix']) ? $configParams['input_prefix'] . '[' . $key . ']' : $key] =
                    (isset($field['input_value'])) ? $field['input_value'] : $field['value'];
            }
        }

        return $params;
    }

    /**
     * Create attribute
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        $url = $_ENV['app_backend_url']
            . 'admin/catalog_category/save/'
            . $fixture->getUrlParams('request_params');
        $params = $this->_prepareData($fixture);
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $params);
        $response = $curl->read();
        $editLink = 'backend/admin/catalog_category/edit/id/';
        $idPosition =  strpos($response, $editLink)+strlen($editLink);
        $id = substr($response, $idPosition, strpos($response, '/', $idPosition)-$idPosition);
        $curl->close();
        return $id;
    }
}
 