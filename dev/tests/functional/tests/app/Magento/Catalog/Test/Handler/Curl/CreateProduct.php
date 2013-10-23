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
 * Class CreateProduct
 */
class CreateProduct extends Curl
{

    /**
     * @var array
     */
    protected $_substitution = array(
        'product[website_ids][]' => array('Yes' => 1),
        'product[stock_data][manage_stock]' => array('Yes' => 1, 'No' => 0),
    );

    /**
     * Returns transformed data
     *
     * @param Fixture $fixture
     * @return mixed
     */
    protected function _prepareData(Fixture $fixture)
    {
        $params = $fixture->getPostParams();
        foreach ($params as $key => $value) {
            if (array_key_exists($key, $this->_substitution) && array_key_exists($value, $this->_substitution[$key])) {
                $params[$key] = $this->_substitution[$key][$value];
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
            . 'admin/catalog_product/save/'
            . $fixture->getUrlParams('create_url_params') . '/popup/1/';
        $params = $this->_prepareData($fixture);
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $params);
        $response = $curl->read();
        $curl->close();

        preg_match("~Location: [^\s]*\/id\/(\d+)~", $response, $matches);
        return isset($matches[1]) ? $matches[1] : null;
    }
}
