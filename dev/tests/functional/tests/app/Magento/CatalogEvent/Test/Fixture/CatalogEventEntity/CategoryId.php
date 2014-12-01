<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;

use Magento\Catalog\Test\Fixture\CatalogCategory;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogProductSimple\CategoryIds;

/**
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
     * @var CatalogCategory
     */
    protected $category;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param int|string $data
     */
    public function __construct(
        FixtureFactory $fixtureFactory,
        array $params,
        $data
    ) {
        $this->params = $params;
        if (!empty($data['presets'])) {
            $preset = $data['presets'];
            $category = $fixtureFactory->createByCode('catalogCategory', ['dataSet' => $preset]);
            $category->persist();

            /** @var CatalogCategory $category */
            $this->data = $category->getName();
            $this->category = $category;
        } else {
            $this->data = $data;
        }
    }

    /**
     * Return category
     *
     * @return CatalogCategory
     */
    public function getCategory()
    {
        return $this->category;
    }
}
