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
use Magento\Catalog\Test\Fixture\CatalogProductSimple\CategoryIds;
use Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;
use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogEventNew;
use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogEventIndex;

/**
 * Test Creation for Update CatalogEventEntity
 *
 * Preconditions:
 * 1. Subcategory is created.
 * 2. Product is created and assigned to subcategory.
 * 3. Catalog event is created and applied for existing category.
 *
 * Test Flow:
 * 1. Log in to backend as admin user.
 * 2. Navigate to MARKETING>Private Sales>Events.
 * 3. Open existing catalog event
 * 4. Fill in all data according to data set
 * 5. Save Event.
 * 6. Perform all assertions.
 *
 * @group Catalog_Events_(MX)
 * @ZephyrId MAGETWO-24576
 */
class UpdateCatalogEventEntityTest extends Injectable
{
    /**
     * Catalog Event Page
     *
     * @var CatalogEventNew
     */
    protected $catalogEventNew;

    /**
     * Catalog Product fixture
     *
     * @var CatalogProductSimple
     */
    protected $product;

    /**
     * Event Page
     *
     * @var CatalogEventIndex
     */
    protected $catalogEventIndex;

    /**
     * Inject data
     *
     * @param CatalogEventNew $catalogEventNew
     * @param CatalogEventIndex $catalogEventIndex
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __inject(
        CatalogEventNew $catalogEventNew,
        CatalogEventIndex $catalogEventIndex,
        FixtureFactory $fixtureFactory
    ) {
        $this->catalogEventNew = $catalogEventNew;
        $this->catalogEventIndex = $catalogEventIndex;

        /** @var CatalogProductSimple $product */
        $product = $fixtureFactory->createByCode('catalogProductSimple', ['dataSet' => 'product_with_category']);
        $product->persist();
        $this->product = $product;

        /** @var CategoryIds $sourceCategories */
        $sourceCategories = $product->getDataFieldConfig('category_ids')['source'];
        $catalogEvent = $fixtureFactory->createByCode(
            'catalogEventEntity',
            [
                'dataSet' => 'default_event',
                'data' => ['category_id' => [$sourceCategories->getIds()[0]]],
            ]
        );
        $catalogEvent->persist();

        return [
            'product' => $product,
            'catalogEventOriginal' => $catalogEvent,
        ];
    }

    /**
     * Update Catalog Event Entity
     *
     * @param CatalogEventEntity $catalogEvent
     * @return void
     */
    public function testUpdateCatalogEvent(CatalogEventEntity $catalogEvent)
    {
        $filter = [
            'category_name' => $this->product->getCategoryIds()[0],
        ];

        //Steps
        $this->catalogEventIndex->open();
        $this->catalogEventIndex->getEventGrid()->searchAndOpen($filter);
        $this->catalogEventNew->getEventForm()->fill($catalogEvent);
        $this->catalogEventNew->getPageActions()->save();
    }
}
