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
 * Class CreateProductAttribute
 */
class CreateProductAttribute extends Curl
{
    /**
     * Create attribute
     *
     * @param Fixture $fixture [optional]
     * @return mixed|string
     */
    public function execute(Fixture $fixture = null)
    {
        $url = $_ENV['app_backend_url'] . 'admin/catalog_product_attribute/save/back/edit/active_tab/main';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $fixture->getPostParams());
        $response = $curl->read();
        $curl->close();

        $id = null;
        if (preg_match('!catalog_product_attribute/save/attribute_id/(\d+)/active_tab/main/!', $response, $matches)) {
            $id = $matches[1];
        }

        $optionIds = array();
        if (preg_match_all(
            '!attributeOption\.add\({"checked":"","intype":"radio","id":"(\d+)"!',
            $response,
            $matches
        )) {
            $optionIds = $matches[1];
        }

        return array('attributeId' => $id, 'optionIds' => $optionIds);
    }
}
