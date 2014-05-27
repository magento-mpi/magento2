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
     * Catalog Event Page
     *
     * @var CatalogEventNew
     */
    protected $catalogEventNew;

    /**
     * Catalog Event Page on the Backend
     *
     * @var CatalogEventIndex
     */
    protected $catalogEventIndex;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param CatalogEventIndex $catalogEventIndex
     * @param CatalogEventNew $catalogEventNew
     * @return array
     */
    public function __inject(
        CatalogEventIndex $catalogEventIndex,
        CatalogEventNew $catalogEventNew,
        FixtureFactory $fixtureFactory
    ) {
        $this->catalogEventIndex = $catalogEventIndex;
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
     * Create Catalog Event Entity from Catalog Event page
     *
     * @param CatalogEventEntity $catalogEvent
     * @param CatalogProductSimple $product
     * @return void
     */
    public function testCreateCatalogEventFromEventPage(
        CatalogEventEntity $catalogEvent,
        CatalogProductSimple $product
    ) {
        //Steps
        $this->catalogEventIndex->open();
        $this->catalogEventIndex->getPageActions()->addNew();
        $this->catalogEventNew->getTreeCategories()
            ->selectCategory(
                $product->getCategoryIds()[0]['path'] . '/' . $product->getCategoryIds()[0]['name']
            );
        $this->catalogEventNew->getEventForm()->fill($catalogEvent);
        $this->catalogEventNew->getPageActions()->save();
    }
}
