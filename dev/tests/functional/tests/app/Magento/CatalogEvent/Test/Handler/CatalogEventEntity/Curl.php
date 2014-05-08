<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Handler\CatalogEventEntity;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlInterface;
use Mtf\Util\Protocol\CurlTransport;
use Mtf\Handler\Curl as AbstractCurl;
use Mtf\Util\Protocol\CurlTransport\BackendDecorator;

/**
 * Class Curl
 * Create Catalog Event
 */
class Curl extends AbstractCurl implements CatalogEventEntityInterface
{
    public function persist(FixtureInterface $fixture = null)
    {
        $data['catalogevent'] = $fixture->getData();
        $data['catalogevent']['display_state']['category_page'] = 1;
        $data['catalogevent']['display_state']['product_page'] = 2;
        $data['catalogevent']['display_state'] = array_values($data['catalogevent']['display_state']);

        $url = $_ENV['app_backend_url'] . 'admin/catalog_event/save/category_id/' . $fixture
                ->getCategoryId() . '/category/1/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->addOption(CURLOPT_HEADER, 1);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        preg_match('#title="http://[^\d]+catalog_event[^\d]+(\d+)[^\d]#Umis', $response, $matches);
        $id = isset($matches[1]) ? $matches[1] : null;
        return ['id' => $id];

    }
}
