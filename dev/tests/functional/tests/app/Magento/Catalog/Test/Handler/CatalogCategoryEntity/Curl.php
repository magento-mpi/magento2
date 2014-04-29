<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Handler\CatalogCategoryEntity;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Class Curl
 *
 * @package Magento\Catalog\Test\Handler\CatalogCategoryEntity
 */
class Curl extends AbstractCurl implements CatalogCategoryEntityInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        $data = [];
        foreach($fixture->getData() as $key => $value){
            $data['general'][$key] = $value;
        }
        $data['use_config'][] = 'available_sort_by';
        $data['use_config'][] = 'default_sort_by';
        $data['use_config'][] = 'filter_price_range';

        $url = $_ENV['app_backend_url'] . 'catalog/category/save/store/0/parent/2/?isAjax=true';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        preg_match('#http://.+/id/(\d+).+isAjax=true#m', $response, $matches);
        $id = isset($matches[1]) ? $matches[1] : null;
        return ['id' => $id];

    }
}
