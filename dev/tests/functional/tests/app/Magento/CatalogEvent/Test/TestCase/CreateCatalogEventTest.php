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
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Adminhtml\CatalogCategoryIndex;
use Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;
use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogEventNew;

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
class CreateCatalogEventTest extends Injectable
{
    /**
     * Category Page
     *
     * @var CatalogCategoryIndex
     */
    protected $catalogCategoryIndex;

    /**
     * New Catalog Event Page
     *
     * @var CatalogEventNew
     */
    protected $adminCatalogEventNew;

    /**
     * Product Page
     *
     * @var CatalogProductSimple
     */
    protected $catalogProductSimple;

    /**
     * @param CatalogCategoryIndex $catalogCategoryIndex
     * @param CatalogEventNew $adminCatalogEventNew
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __inject(
        CatalogCategoryIndex $catalogCategoryIndex,
        CatalogEventNew $adminCatalogEventNew,
        FixtureFactory $fixtureFactory
    ) {
        $this->catalogCategoryIndex = $catalogCategoryIndex;
        $this->adminCatalogEventNew = $adminCatalogEventNew;

        /**@var CatalogProductSimple $catalogProductSimple */
        $catalogProductSimple = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'product_with_category']
        );
        $catalogProductSimple->persist();

        return [
            'catalogProductSimple' => $catalogProductSimple
        ];
    }

    /**
     * Create Catalog Event Entity from Category page
     *
     * @param CatalogEventEntity $catalogEvent
     * @param CatalogProductSimple $catalogProductSimple
     */
    public function testCreateCatalogEvent(
    CatalogEventEntity $catalogEvent,
        CatalogProductSimple $catalogProductSimple
    ) {
        $categoryName = $catalogProductSimple->getDataFieldConfig('category_ids')['fixture']
            ->getCategory()[0]->getName();

        //Steps
        $this->catalogCategoryIndex->open();
        $this->catalogCategoryIndex->getTreeCategories()->selectCategory("Default Category/$categoryName");
        $this->catalogCategoryIndex->getPageActionsEvent()->addEventNew();
        $this->adminCatalogEventNew->getEventForm()->fill($catalogEvent);

        $this->adminCatalogEventNew->getPageActions()->save();
    }
}
