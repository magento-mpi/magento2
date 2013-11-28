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

namespace Magento\Core\Test\Handler\Curl;

use Mtf\Fixture;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Curl handler for persisting Magento store
 *
 * @package Magento\Core\Test\Handler\Curl
 */
class CreateStore extends Curl
{
    /**
     * Post request for persisting Magento Store
     *
     * @param Fixture $fixture
     * @return array
     * @throws \UnexpectedValueException
     */
    public function execute(Fixture $fixture = null)
    {
        $data = $fixture->getData();
        $fields = array(
            'group' => $data,
            'store_action' => 'add',
            'store_type' => 'group',
        );

        $url = $_ENV['app_backend_url'] . 'admin/system_store/save/';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $fields);
        $curl->read();
        $curl->close();
        $data['id'] = $this->_getStoreIdByStoreName($data['name']);

        return $data;
    }

    /**
     * Get store id by store name
     *
     * @param string $storeName
     * @return int
     * @throws \UnexpectedValueException
     */
    protected function _getStoreIdByStoreName($storeName)
    {
        //Set pager limit to 2000 in order to find created store by name
        $url = $_ENV['app_backend_url'] . 'admin/system_store/index/sort/website_title/dir/asc/limit/2000';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0');
        $response = $curl->read();

        $expectedUrl = $_ENV['app_backend_url'] . 'admin/system_store/editGroup/group_id/';
        $expectedUrl = preg_quote($expectedUrl);
        $expectedUrl = str_replace('/', '\/', $expectedUrl);
        preg_match('/' . $expectedUrl . '([0-9]*)\/(.)*>' . $storeName . '<\/a>/', $response, $matches);

        if (empty($matches)) {
            throw new \UnexpectedValueException('Cannot find store id');
        }

        return intval($matches[1]);
    }
}