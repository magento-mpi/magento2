<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Fixture\Category;

use Magento\Catalog\Test\Fixture\CatalogCategory;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogProductSimple\CategoryIds;

/**
 * Class CategoryId
 * Create and return Category
 */
class CategoryId extends CategoryIds
{
    /**
     * Names and Ids of the created categories
     *
     * @var array
     */
    protected $data;

    /**
     * Fixtures of category
     *
     * @var array
     */
    protected $categories;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(
        FixtureFactory $fixtureFactory,
        array $params,
        array $data = []
    ) {
        $this->params = $params;
        if (!empty($data['presets'])) {
            $preset = $data['presets'];
            $category = $fixtureFactory->createByCode('catalogCategory', ['dataSet' => $preset]);
            $category->persist();

            /** @var CatalogCategory $category */
            $this->data = $category->getName();
            $this->categories = $category;
        } else {
            $this->data = $data[0];
        }
    }
}
