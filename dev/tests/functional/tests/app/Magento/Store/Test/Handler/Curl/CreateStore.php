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

namespace Magento\Store\Test\Handler\Curl;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Curl;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;
use Mtf\System\Config;

/**
 * Curl handler for persisting Magento store view
 *
 * @package Magento\Store\Test\Handler\Curl
 */
class CreateStore extends Curl
{
    /**
     * Prepare POST data for creating store view request
     *
     * @param array $params
     * @return array
     */
    protected function _prepareData($params)
    {
        $data = array();
        foreach ($params['fields'] as $name => $config) {
            $data[$name] = $config['value'];
        }
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
        //Set pager limit to 2000 in order to find created store view by name
        $url = $_ENV['app_backend_url'] . 'admin/system_store/index/sort/store_title/dir/asc/limit/2000';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0');
        $response = $curl->read();

        $expectedUrl = '/admin/system_store/editStore/store_id/';
        $expectedUrl = preg_quote($expectedUrl);
        $expectedUrl = str_replace('/', '\/', $expectedUrl);
        preg_match('/' . $expectedUrl . '([0-9]*)\/(.)*>' . $storeName . '<\/a>/', $response, $matches);

        if (empty($matches)) {
            throw new \UnexpectedValueException('Cannot find store id');
        }

        return intval($matches[1]);
    }

    /**
     * Post request for persisting Magento Store View
     *
     * @param FixtureInterface $fixture
     * @return array
     * @throws \UnexpectedValueException
     * @throws \UnderflowException
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = $this->_prepareData($fixture->getData());
        $data['store_id'] = '';
        $fields = array(
            'store' => $data,
            'store_action' => 'add',
            'store_type' => 'store',
        );

        $url = $_ENV['app_backend_url'] . 'admin/system_store/save/';
        $curl = new BackendDecorator(new CurlTransport(), new Config());
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $fields);
        $response = $curl->read();
        $curl->close();

        if (!preg_match('/The store view has been saved/', $response)) {
            throw new \UnderflowException('Store was\'t saved');
        }

        $data['id'] = $this->_getStoreIdByStoreName($fixture->getData('fields/name/value'));

        return $data;
    }
}

