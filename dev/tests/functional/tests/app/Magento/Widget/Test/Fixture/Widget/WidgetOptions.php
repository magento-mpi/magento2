<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Widget\Test\Fixture\Widget;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Prepare Widget options for widget
 */
class WidgetOptions implements FixtureInterface
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
     * Widget option entities
     *
     * @var array
     */
    protected $entities;

    /**
     * Constructor
     *
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['preset'])) {
            $this->data = $this->getPreset($data['preset']);
            $this->data[0]['type_id'] = $data['preset'];
            foreach ($this->data[0] as $key => $value) {
                if ($key == 'entities') {
                    foreach ($value as $index => $entity) {
                        $explodeValue = explode('::', $entity);
                        $fixture = $fixtureFactory->createByCode($explodeValue[0], ['dataSet' => $explodeValue[1]]);
                        $fixture->persist();
                        $this->data[0]['entities'][$index] = $fixture;
                        $this->entities[] = $fixture;
                    }
                }
            }
        } else {
            $this->data = $data;
        }
    }

    /**
     * Persist Widget Options
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
     * Preset for Widget options
     *
     * @param string $name
     * @return array|null
     */
    protected function getPreset($name)
    {
        $presets = [
            'cmsPageLink' => [
                [
                    'anchor_text' => 'CustomText_%isolation%',
                    'title' => 'CustomTitle_%isolation%',
                    'entities' => ['cmsPage::default'],
                ],
            ],
            'cmsStaticBlock' => [
                [
                    'chooser_title' => '%title%',
                    'chooser_identifier' => '%identifier%',
                    'entities' => ['cmsBlock::default'],
                ],
            ],
            'catalogCategoryLink' => [
                [
                    'anchor_text' => 'CustomText_%isolation%',
                    'title' => 'CustomTitle_%isolation%',
                    'entities' => ['catalogCategory::default'],
                ],
            ],
            'catalogNewProductList' => [
                [
                    'display_type' => 'All products',
                    'show_pager' => 'Yes',
                    'products_count' => '4',
                ],
            ],
            'catalogProductLink' => [
                [
                    'anchor_text' => 'CustomText_%isolation%',
                    'title' => 'CustomTitle_%isolation%',
                    'entities' => ['catalogProductSimple::default'],
                ],
            ],
            'recentlyComparedProducts' => [
                [
                    'page_size' => '4',
                ],
            ],
            'recentlyViewedProducts' => [
                [
                    'page_size' => '4',
                ],
            ],
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }

    /**
     * Return entities
     *
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }
}
