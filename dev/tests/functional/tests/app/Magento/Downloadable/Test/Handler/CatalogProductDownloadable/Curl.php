<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Handler\CatalogProductDownloadable;

use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Handler\CatalogProductSimple\Curl as AbstractCurl;

/**
 * Class Curl
 * Create new downloadable product via curl
 */
class Curl extends AbstractCurl implements CatalogProductDownloadableInterface
{
    /**
     * Post request for creating downloadable product
     *
     * @param FixtureInterface $fixture [optional]
     * @return array
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $this->extendPlaceholder();
        $config = $fixture->getDataConfig();
        $prefix = isset($config['input_prefix']) ? $config['input_prefix'] : null;
        $data = $this->prepareData($fixture, $prefix);

        if ($prefix) {
            $data['downloadable'] = $data[$prefix]['downloadable'];
            unset($data[$prefix]['downloadable']);
        }

        return ['id' => $this->createProduct($data, $config)];
    }

    /**
     * Expand basic placeholder
     *
     * @return void
     */
    protected function extendPlaceholder()
    {
        $this->mappingData += [
            'links_purchased_separately' => [
                'Yes' => 1,
                'No' => 0
            ],
            'is_shareable' => [
                'Yes' => 1,
                'No' => 0,
                'Use config' => 2
            ],
        ];
    }
}
