<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Handler\CatalogProductDownloadable;

use Mtf\System\Config;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Handler\CatalogProductSimple\Curl as ProductCurl;

/**
 * Class Curl
 * Create new downloadable product via curl
 */
class Curl extends ProductCurl implements CatalogProductDownloadableInterface
{
    /**
     * Constructor
     *
     * @param Config $configuration
     */
    public function __construct(Config $configuration)
    {
        parent::__construct($configuration);

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

    /**
     * Prepare POST data for creating product request
     *
     * @param FixtureInterface $fixture
     * @param string|null $prefix [optional]
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture, $prefix = null)
    {
        $data = parent::prepareData($fixture, null);

        $downloadableData = [];
        if (!empty($data['downloadable_links'])) {
            $data['links_title'] = $data['downloadable_links']['title'];
            foreach ($data['downloadable_links']['downloadable']['link'] as $key => $link) {
                $downloadableData['downloadable']['link'][$key]['title'] = $link['title'];
                // only url type
                $downloadableData['downloadable']['link'][$key]['type'] = 'url';
                $downloadableData['downloadable']['link'][$key]['link_url'] = $link['file_link_url'];
                $downloadableData['downloadable']['link'][$key]['price'] = $link['price'];
                $downloadableData['downloadable']['link'][$key]['number_of_downloads'] = $link['number_of_downloads'];
                $downloadableData['downloadable']['link'][$key]['is_shareable'] = $link['is_shareable'];
                $downloadableData['downloadable']['link'][$key]['sort_order'] = $link['sort_order'];
                // only url type
                $downloadableData['downloadable']['link'][$key]['sample']['type'] = 'url';
                $downloadableData['downloadable']['link'][$key]['sample']['url'] = $link['sample']['sample_url'];
            }
            unset($data['downloadable_links']);
        }

        if (!empty($data['downloadable_sample'])) {
            $data['samples_title'] = $data['downloadable_sample']['title'];
            foreach ($data['downloadable_sample']['downloadable']['sample'] as $key => $sample) {
                $downloadableData['downloadable']['sample'][$key]['title'] = $sample['title'];
                // only url type
                $downloadableData['downloadable']['sample'][$key]['type'] = 'url';
                $downloadableData['downloadable']['sample'][$key]['sample_url'] = $sample['sample_url'];
                $downloadableData['downloadable']['sample'][$key]['sort_order'] = $sample['sort_order'];
            }
            unset($data['downloadable_sample']);
        }

        $data = $prefix ? [$prefix => $data] : $data;
        $data = array_merge($data, $downloadableData);

        return $this->replaceMappingData($data);
    }
}
