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
 * Create new category via curl
 */
class Curl extends AbstractCurl implements CatalogCategoryEntityInterface
{
    /**
     * Data use config for category
     *
     * @var array
     */
    protected $dataUseConfig = [
        'available_sort_by',
        'default_sort_by',
        'filter_price_range',
    ];

    /**
     * Post request for creating Subcategory
     *
     * @param FixtureInterface $fixture [optional]
     * @return mixed|string
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $data['general'] = $fixture->getData();
        foreach ($data['general'] as $key => $value) {
            if ($value == 'Yes') {
                $data['general'][$key] = 1;
            }
            if ($value == 'No') {
                $data['general'][$key] = 0;
            }
        }

        $diff = array_diff($this->dataUseConfig, array_keys($data['general']));
        if (!empty($diff)) {
            $data['use_config'] = $diff;
        }
        $parentCategoryId = $data['general']['parent_id'];

        $url = $_ENV['app_backend_url'] . 'catalog/category/save/store/0/parent/' . $parentCategoryId . '/';
        $curl = new BackendDecorator(new CurlTransport(), new Config);
        $curl->write(CurlInterface::POST, $url, '1.0', array(), $data);
        $response = $curl->read();
        $curl->close();

        preg_match('#http://.+/id/(\d+).+store/#m', $response, $matches);
        $id = isset($matches[1]) ? $matches[1] : null;
        return ['id' => $id];
    }
}
