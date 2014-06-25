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
 * Class Samples
 *
 * Preset for sample block
 */
class Samples implements FixtureInterface
{
    /**
     * Construct for class
     *
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
     * @param string $key [optional]
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'default' => [
                'title' => 'Samples%isolation%',
                'downloadable' => [
                    'sample' => [
                        [
                            'title' => 'sample1%isolation%',
                            'sample_type_url' => 'Yes',
                            'sample_url' => 'http://example.com',
                            'sort_order' => 0
                        ],
                        [
                            'title' => 'sample2%isolation%',
                            'sample_type_url' => 'Yes',
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
                            'title' => 'sample1%isolation%',
                            'sample_type_url' => 'Yes',
                            'sample_url' => 'http://example.com',
                            'sort_order' => 0
                        ],
                        [
                            'title' => 'sample2%isolation%',
                            'sample_type_url' => 'Yes',
                            'sample_url' => 'http://example2.com',
                            'sort_order' => 1
                        ],
                        [
                            'title' => 'sample3%isolation%',
                            'sample_type_url' => 'Yes',
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
