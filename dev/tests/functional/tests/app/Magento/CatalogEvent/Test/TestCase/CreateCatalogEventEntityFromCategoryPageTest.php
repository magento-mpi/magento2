<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogEvent\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;
use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogEventNew;
use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogCategoryIndex;

/**
 * Test Creation for Create CatalogEventEntity from Category page
 *
 * Test Flow:
 * 1. Log in to backend as admin user.
 * 2. Navigate to Products>Inventory>Categories
 * 3. Select created category.
 * 4. Click "Add Event..". button.
 * 5. Fill in all data according to data set.
 * 6. Save Event.
 * 7. Perform all assertions.
 *
 * @group Catalog_Events_(MX)
 * @ZephyrId MAGETWO-23423
 */
class CreateCatalogEventEntityFromCategoryPageTest extends Injectable
{
    /**
     * Category Page
     *
     * @var CatalogCategoryIndex
     */
    protected $catalogCategoryIndex;

    /**
     * Catalog Event Page
     *
     * @var CatalogEventNew
     */
    protected $catalogEventNew;

    /**
     * Product simple fixture
     *
     * @var CatalogProductSimple
     */
    protected $catalogProductSimple;

    /**
     * @param CatalogCategoryIndex $catalogCategoryIndex
     * @param CatalogEventNew $catalogEventNew
     * @param FixtureFactory $fixtureFactory
     *
     * @return array
     */
    public function __inject(
        CatalogCategoryIndex $catalogCategoryIndex,
        CatalogEventNew $catalogEventNew,
        FixtureFactory $fixtureFactory
    ) {
        $this->catalogCategoryIndex = $catalogCategoryIndex;
        $this->catalogEventNew = $catalogEventNew;

        /**@var CatalogProductSimple $catalogProductSimple */
        $product = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'product_with_category']
        );
        $product->persist();

        return [
            'product' => $product
        ];
    }

    /**
     * Create Catalog Event Entity from Category page
     *
     * @param CatalogEventEntity $catalogEvent
     * @param CatalogProductSimple $product
     * @param CatalogCategory $catalogCategory
     *
     * @return void
     */
    public function testCreateCatalogEvent(
        CatalogEventEntity $catalogEvent,
        CatalogProductSimple $product,
        CatalogCategory $catalogCategory
    ) {
        //Steps
        $this->catalogCategoryIndex->open();
        $this->catalogCategoryIndex->getTreeCategories()
            ->selectCategory(
                $catalogCategory->getPath() . '/' . $product->getCategoryIds()[0]['name']
            );
        $this->catalogCategoryIndex->getPageActionsEvent()->addCatalogEvent();
        $this->catalogEventNew->getEventForm()->fill($catalogEvent);
        $this->catalogEventNew->getPageActions()->save();
    }
}
