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
    /**
     * Post request for creating Event
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data = ['catalogevent' => $fixture->getData()];

        if ($data['catalogevent']['display_state']['category_page'] === 'Yes') {
            $data['catalogevent']['display_state']['category_page'] = 1;
        }
        if ($data['catalogevent']['display_state']['product_page'] === 'Yes') {
            $data['catalogevent']['display_state']['product_page'] = 2;
        }
        $data['catalogevent']['display_state'] = array_values($data['catalogevent']['display_state']);
        if ($fixture->getDataFieldConfig('category_id')['source']->getCategory() === null) {
            $categoryId = $fixture->getCategoryId();
        } else {
            $categoryId = $fixture->getDataFieldConfig('category_id')['source']->getCategory()->getId();
        }
        $url = $_ENV['app_backend_url'] . 'admin/catalog_event/save/category_id/'
            . $categoryId . '/category/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', [], $data);
        $response = $curl->read();
        $curl->close();
        preg_match(
            '/class=\"\scol\-id col\-event_id\W*>\W+(\d+)\W+<\/td>\W+<td[\w\s\"=\-]*?>\W+?'
            . $categoryId . '/siu',
            $response,
            $matches
        );
        $id = isset($matches[1]) ? $matches[1] : null;

        return ['id' => $id];
    }
}
