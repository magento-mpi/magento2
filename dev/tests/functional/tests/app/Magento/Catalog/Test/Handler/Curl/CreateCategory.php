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
     * @var array
     */
    protected $_substitution = array(
        'general[path]' => '2',
        'general[store]' => '0',
        'general[is_active]' => array('Yes' => 1, 'No' => 0),
        'general[include_in_menu]' => array('Yes' => 1, 'No' => 0)
    );

    /**
     * Returns transformed data
     *
     * @param Fixture $fixture
     * @return mixed
     */
    protected function _prepareData(Fixture $fixture)
    {
        $sub = array(
            'use_config[available_sort_by]' => '1',
            'use_config[default_sort_by]' => '1'
        );

        $params = $fixture->getPostParams();
        foreach ($params as $key => $value) {
            if (array_key_exists($key, $this->_substitution) && array_key_exists($value, $this->_substitution[$key])) {
                $params[$key] = $this->_substitution[$key][$value];
            }
        }
        $params['general[path]'] = $this->_substitution['general[path]'];
        $params['general[store]'] = $this->_substitution['general[store]'];
        $params = array_merge($params, $sub);
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
        $curl->close();
        return $response;
    }
}
