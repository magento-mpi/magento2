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
 * Class Links
 *
 * Preset for link block
 */
class Links implements FixtureInterface
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
     * Preset array for downloadable links
     *
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'default' => [
                'title' => 'Links%isolation%',
                'links_purchased_separately' => 'Yes',
                'downloadable' => [
                    'link' => [
                        [
                            'title' => 'link1%isolation%',
                            'price' => 2.43,
                            'number_of_downloads' => 2,
                            'sample' => [
                                'sample_type_url' => 'Yes',
                                'sample_url' => 'http://example.com'
                            ],
                            'file_type_url' => 'Yes',
                            'file_link_url' => 'http://example.com',
                            'is_shareable' => 'No',
                            'sort_order' => 1
                        ],
                        [
                            'title' => 'link2%isolation%',
                            'price' => 3,
                            'number_of_downloads' => 3,
                            'sample' => [
                                'sample_type_url' => 'Yes',
                                'sample_url' => 'http://example3.com'
                            ],
                            'file_type_url' => 'Yes',
                            'file_link_url' => 'http://example3.com',
                            'is_shareable' => 'Yes',
                            'sort_order' => 0
                        ],
                    ]
                ]
            ],
            'with_three_links' => [
                'title' => 'Links%isolation%',
                'links_purchased_separately' => 'Yes',
                'downloadable' => [
                    'link' => [
                        [
                            'title' => 'link1%isolation%',
                            'price' => 2.43,
                            'number_of_downloads' => 2,
                            'sample' => [
                                'sample_type_url' => 'Yes',
                                'sample_url' => 'http://example.com'
                            ],
                            'file_type_url' => 'Yes',
                            'file_link_url' => 'http://example.com',
                            'is_shareable' => 'No',
                            'sort_order' => 0
                        ],
                        [
                            'title' => 'link2%isolation%',
                            'price' => 3,
                            'number_of_downloads' => 3,
                            'sample' => [
                                'sample_type_url' => 'Yes',
                                'sample_url' => 'http://example3.com'
                            ],
                            'file_type_url' => 'Yes',
                            'file_link_url' => 'http://example3.com',
                            'is_shareable' => 'Yes',
                            'sort_order' => 1
                        ],
                        [
                            'title' => 'link3%isolation%',
                            'price' => 5.43,
                            'number_of_downloads' => 5,
                            'sample' => [
                                'sample_type_url' => 'Yes',
                                'sample_url' => 'http://example3.com'
                            ],
                            'file_type_url' => 'Yes',
                            'file_link_url' => 'http://example3.com',
                            'is_shareable' => 'Yes',
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
