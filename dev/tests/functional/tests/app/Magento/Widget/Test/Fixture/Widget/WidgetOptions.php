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
     * @param array $params
     * @param array $data
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
                    unset($this->data[0]['entities']);
                    if (!empty($explodeValue[2])) {
                        for ($i = 0; $i < $explodeValue[2]; $i++) {
                            $fixture = $fixtureFactory
                                ->createByCode($explodeValue[0], ['dataSet' => $explodeValue[1]]);
                            $fixture->persist();
                            $this->data[0]['entities'][] = $fixture;
                        }
                    } else {
                        $fixture = $fixtureFactory
                            ->createByCode($explodeValue[0], ['dataSet' => $explodeValue[1]]);
                        $fixture->persist();
                        $this->data[0]['entities'][] = $fixture;
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
            'recentlyComparedProducts' => [
                [
                    'page_size' => '4',
                    'entities' => 'catalogProductSimple::default::2'
                ]
            ],
            'recentlyViewedProducts' => [
                [
                    'page_size' => '4',
                    'entities' => 'catalogProductSimple::product_with_category'
                ]
            ],
        ];

        if (!isset($presets[$name])) {
            return null;
        }

        return $presets[$name];
    }
}
