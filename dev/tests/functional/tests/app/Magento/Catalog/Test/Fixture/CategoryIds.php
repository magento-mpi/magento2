<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class CategoryIds
 *
 * Data keys:
 *  - preset (Price verification preset name)
 *  - value (Price value)
 */
class CategoryIds implements FixtureInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var \Mtf\Fixture\FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @var Category
     */
    protected $category;

    /**
     * @param CatalogCategoryEntity $category
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(
        CatalogCategoryEntity $category,
        FixtureFactory $fixtureFactory,
        array $params,
        array $data = []
    ) {
        $this->params = $params;
        if (isset($data['presets']) && $data['presets'] !== '-') {
            $presets = explode(',', $data['presets']);
            foreach ($presets as $preset) {
                $category = $fixtureFactory->createByCode('catalogCategoryEntity', ['dataSet' => $preset]);
                $category->persist();

                /** @var CatalogCategoryEntity $category */
                $this->data[] = $category->getId();
                $this->data[] = $category->getName();
                $this->category[] = $category;
            }
        }
    }

    /**
     * Persist custom selections products
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
     * Retrieve source category fixture
     *
     * @return array
     */
    public function getCategory()
    {
        return $this->category;
    }
}
