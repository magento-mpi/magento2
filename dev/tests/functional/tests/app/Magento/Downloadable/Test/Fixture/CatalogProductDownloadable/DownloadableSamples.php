<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Downloadable\Test\Fixture\CatalogProductDownloadable;

use Mtf\Fixture\FixtureInterface;

/**
 * Class DownloadableSamples
 *
 * Data keys:
 *  - link (link options preset name)
 *  - products (comma separated sku identifiers)
 *
 * @package Magento\Catalog\Test\Fixture
 */
class DownloadableSamples implements FixtureInterface
{
    /**
     * @var \Mtf\Fixture\FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @param array $params
     * @param array $data
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['preset'])) {
            $this->data = $this->getPreset($data['preset']);
        }
    }

    /**
     * Persist group price
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set
     *
     * @param $key [optional]
     * @return mixed
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Preset array for downloadable samples
     *
     * @param string $name
     * @return array|bool
     */
    protected function getPreset($name)
    {
        $presets = [
            'default' => [
                'title' => 'Samples%isolation%',
                'downloadable' => [
                    'sample' => [
                        [
                            'title' => 'row1%isolation%',
                            'type' => 'url',
                            'sample_url' => 'http://example.com',
                            'sort_order' => 0
                        ],
                        [
                            'title' => 'row2%isolation%',
                            'type' => 'url',
                            'sample_url' => 'http://example2.com',
                            'sort_order' => 1
                        ]
                    ]
                ]
            ],
            'with_three_samples' => [
                'title' => 'Samples%isolation%',
                'downloadable' => [
                    'sample' => [
                        [
                            'title' => 'row1%isolation%',
                            'type' => 'url',
                            'sample_url' => 'http://example.com',
                            'sort_order' => 0
                        ],
                        [
                            'title' => 'row2%isolation%',
                            'type' => 'url',
                            'sample_url' => 'http://example2.com',
                            'sort_order' => 1
                        ],
                        [
                            'title' => 'row3%isolation%',
                            'type' => 'url',
                            'sample_url' => 'http://example3.com',
                            'sort_order' => 2
                        ]
                    ]
                ]
            ]
        ];
        if (!isset($presets[$name])) {
            return null;
        }
        return $presets[$name];
    }
}
