<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Fixture\Widget;

use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\FixtureFactory;

/**
 * Class LayoutUpdates
 * Prepare Layout Updates for widget
 */
class LayoutUpdates implements FixtureInterface
{
    /**
     * Prepared dataSet data
     *
     * @var array
     */
    protected $data;

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

    /**
     * Constructor
     *
     * @param array $params
     * @param array $data
     * @param FixtureFactory $fixtureFactory
     */
    public function __construct(array $params, FixtureFactory $fixtureFactory, array $data = [])
    {
        $this->params = $params;
        if (isset($data['preset'])) {
            $this->data = $this->getPreset($data['preset']);
            foreach ($this->data as $index => $layouts) {
                foreach ($layouts as $key => $value) {
                    if ($key == 'entities') {
                        $explodeValue = explode('::', $value);
                        $fixture = $fixtureFactory
                            ->createByCode($explodeValue[0], ['dataSet' => $explodeValue[1]]);
                        $fixture->persist();
                        $this->data[$index]['entities'] = $fixture->getData();
                    }
                }
            }
        } else {
            $this->data = $data;
        }
    }

    /**
     * Persist Layout Updates
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
     * @param string|null $key [optional]
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
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Preset for Layout Updates
     *
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'all_pages' => [
                [
                    'page_group' => 'All Pages',
                    'block' => 'Main Content Area',
                    'template' => 'Banner Block Template',
                    'entities' => 'catalogCategory::default',
                ]
            ],
            'on_category' => [
                [
                    'page_group' => 'Non-Anchor Categories',
                    'for' => 'Yes',
                    'entities' => 'catalogCategory::default',
                    'block' => 'Main Content Area',
                    'template' => 'Banner Block Template'
                ]
            ],
            'for_virtual_product' => [
                [
                    'page_group' => 'Virtual Product',
                    'for' => 'Yes',
                    'entities' => 'catalogProductVirtual::default',
                    'block' => 'Main Content Area',
                    'template' => 'Banner Block Template'
                ]
            ],
            'for_category_link' => [
                [
                    'page_group' => 'All Pages',
                    'block' => 'Main Content Area',
                    'template' => 'Category Link Block Template'
                ]
            ],
            'on_product_link' => [
                [
                    'page_group' => 'Non-Anchor Categories',
                    'for' => 'Yes',
                    'entities' => 'catalogCategory::default',
                    'block' => 'Main Content Area',
                    'template' => 'Product Link Block Template'
                ]
            ],
            'for_compared_products' => [
                [
                    'page_group' => 'All Pages',
                    'block' => 'Main Content Area',
                ]
            ],
            'for_viewed_products' => [
                [
                    'page_group' => 'All Pages',
                    'block' => 'Main Content Area',
                ]
            ],
            'for_cms_page_link' => [
                [
                    'page_group' => 'All Pages',
                    'block' => 'Main Content Area',
                    'template' => 'CMS Page Link Block Template',
                    'entities' => 'catalogCategory::default',
                ]
            ],
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
