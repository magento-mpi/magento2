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
use Magento\Catalog\Test\Fixture\CatalogCategoryEntity;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;
use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogEventNew;
use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogEventIndex;

/**
 * Test Creation for CreateCatalogEventEntity from Catalog Event Page
 *
 * Test Flow:
 * 1. Log in to backend as admin user.
 * 2. Navigate to MARKETING>Private Sales>Events.
 * 3. Start new Event creation.
 * 4. Choose category from precondition.
 * 5. Fill in all data according to data set.
 * 6. Save Event.
 * 7. Perform all assertions.
 *
 * @group Catalog_Events_(MX)
 * @ZephyrId MAGETWO-24573
 */
class CreateCatalogEventEntityFromCatalogEventPage extends Injectable
{
    /**
     * Product simple fixture
     *
     * @var CatalogProductSimple
     */
    protected $catalogProductSimple;

    /**
     * @param FixtureFactory $fixtureFactory
     *
     * @return array
     */
    public function __inject(
        FixtureFactory $fixtureFactory
    ) {
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
     * Create Catalog Event Entity from Catalog Event page
     *
     * @param CatalogEventEntity $catalogEvent
     * @param CatalogProductSimple $catalogProductSimple
     * @param CatalogCategoryEntity $catalogCategoryEntity
     * @param CatalogEventIndex $catalogEventIndex
     * @param CatalogEventNew $catalogEventNew
     *
     * @return void
     */
    public function testCreateCatalogEventFromEventPage(
        CatalogEventEntity $catalogEvent,
        CatalogProductSimple $catalogProductSimple,
        CatalogCategoryEntity $catalogCategoryEntity,
        CatalogEventIndex $catalogEventIndex,
        CatalogEventNew $catalogEventNew
    ) {
        //Steps
        $catalogEventIndex->open();
        $catalogEventIndex->getBlockPageActionsEvent()->addNew();
        $catalogEventNew->getTreeCategories()
            ->selectCategory(
                $catalogCategoryEntity->getPath() . '/' . $catalogProductSimple->getCategoryIds()[0]['name']
            );
        $catalogEventNew->getEventForm()->fill($catalogEvent);
        $catalogEventNew->getPageActions()->save();
    }
}
