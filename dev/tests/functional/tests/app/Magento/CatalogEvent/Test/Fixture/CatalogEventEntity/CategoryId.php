<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;

use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Catalog\Test\Fixture\CatalogProductSimple\CategoryIds;
use Mtf\Fixture\FixtureFactory;

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
