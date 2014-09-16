<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Fixture\Widget;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CatalogRule\Test\Fixture\CatalogPriceRule;
use Magento\Cms\Test\Fixture\CmsBlock;
use Magento\SalesRule\Test\Fixture\SalesRuleInjectable;
use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\FixtureFactory;

/**
 * Class WidgetOptions
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
     * Constructor
     *
     * @param array $params [optional]
     * @param array $data [optional]
     * @param FixtureFactory $fixtureFactory
     */
    public function __construct(array $params, FixtureFactory $fixtureFactory, array $data = [])
    {
        $this->params = $params;
        if (isset($data['preset'])) {
            $this->data = $this->getPreset($data['preset']);
            $this->data[0]['name'] = $data['preset'];
            foreach ($this->data[0] as $key => $value) {
                if ($key == 'entities') {
                    $explodeValue = explode('::', $value);
                    if (!empty($explodeValue[2])) {
                        for ($i = 1; $i <= $explodeValue[2]; $i++) {
                            $fixture = $fixtureFactory
                                ->createByCode($explodeValue[0], ['dataSet' => $explodeValue[1]]);
                            $fixture->persist();
                            $this->data[0]['entities'] = $fixture->getData();
                        }
                    } else {
                        $fixture = $fixtureFactory
                            ->createByCode($explodeValue[0], ['dataSet' => $explodeValue[1]]);
                        $fixture->persist();
                        $this->data[0]['entities'] = $fixture->getData();
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
            'bannerRotator' => [
                [
                    'display_mode' => 'Specified Banners',
                    'rotate' => 'Display all instead of rotating.',
                    'entities' => 'bannerInjectable::default',
                ]
            ],
            'bannerRotatorShoppingCartRules' => [
                [
                    'display_mode' => 'Specified Banners',
                    'rotate' => 'Display all instead of rotating.',
                    'entities' => 'bannerInjectable::banner_rotator_shopping_cart_rules',
                ]
            ],
            'bannerRotatorCatalogRules' => [
                [
                    'display_mode' => 'Specified Banners',
                    'rotate' => 'Display all instead of rotating.',
                    'entities' => 'bannerInjectable::banner_rotator_catalog_rules',
                ]
            ],
            'hierarchyNodeLink' => [
                [
                    'anchor_text' => 'CustomText_%isolation%',
                    'title' => 'CustomTitle_%isolation%',
                    'node' => '%node_name%',
                    'entities' => 'cmsHierarchy::cmsHierarchy'
                ]
            ],
            'cmsPageLink' => [
                [
                    'anchor_text' => 'CustomText_%isolation%',
                    'title' => 'CustomTitle_%isolation%',
                    'entities' => 'cmsPage::default',
                ]
            ],
            'cmsStaticBlock' => [
                [
                    'chooser_title' => '%title%',
                    'chooser_identifier' => '%identifier%',
                    'entities' => 'cmsBlock::default',
                ]
            ],
            'catalogCategoryLink' => [
                [
                    'anchor_text' => 'CustomText_%isolation%',
                    'title' => 'CustomTitle_%isolation%',
                    'entities' => 'catalogCategory::default',
                ]
            ],
            'catalogEventCarousel' => [
                [
                    'limit' => '6',
                    'scroll' => '3',
                    'width' => '4',
                    'entities' => 'catalogEventEntity::default_event::2',
                ]
            ],
            'catalogNewProductList' => [
                [
                    'display_type' => 'All products',
                    'show_pager' => 'Yes',
                    'products_count' => '4',
                ]
            ],
            'catalogProductLink' => [
                [
                    'anchor_text' => 'CustomText_%isolation%',
                    'title' => 'CustomTitle_%isolation%',
                    'entities' => 'catalogProductSimple::default',
                ]
            ],
            'giftRegistrySearch' => [
                [
                    'types' => 'All Forms',
                ]
            ],
            'orderBySku' => [
                [
                    'link_display' => 'Yes',
                    'link_text' => 'text%isolation%'
                ]
            ],
            'recentlyComparedProducts' => [
                [
                    'page_size' => '4',
                ]
            ],
            'recentlyViewedProducts' => [
                [
                    'page_size' => '4',
                ]
            ],
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
