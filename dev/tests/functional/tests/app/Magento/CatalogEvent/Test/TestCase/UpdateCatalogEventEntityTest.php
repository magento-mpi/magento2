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
use Magento\CatalogEvent\Test\Fixture\CatalogEventEntity;
use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogEventNew;
use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogEventIndex;

/**
 * Test Creation for Update CatalogEventEntity
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
 * @ZephyrId  MAGETWO-24576
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
     * Event Page
     *
     * @var CatalogEventIndex
     */
    protected $catalogEventIndex;

    /**
     * @param CatalogEventNew $catalogEventNew
     * @param CatalogEventIndex $catalogEventIndex
     * @param FixtureFactory $fixtureFactory
     *
     * @return array
     */
    public function __inject(
        CatalogEventNew $catalogEventNew,
        CatalogEventIndex $catalogEventIndex,
        FixtureFactory $fixtureFactory
    ) {
        $this->catalogEventNew = $catalogEventNew;
        $this->catalogEventIndex = $catalogEventIndex;

        /** @var CatalogProductSimple $catalogProductSimple */
        $catalogProductSimple = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'product_with_category']
        );
        $catalogProductSimple->persist();

        $categoryId = $catalogProductSimple->getCategoryIds()[0]['id'];
        $catalogEventEntity = $fixtureFactory->createByCode(
            'catalogEventEntity',
            [
                'dataSet' => 'new_event',
                'data' => ['category_id' => $categoryId],
            ]
        );
        $catalogEventEntity->persist();

        return [
            'catalogProductSimple' => $catalogProductSimple,
            'catalogEventEntity' => $catalogEventEntity,
        ];
    }

    /**
     * @param CatalogEventEntity $catalogEvent
     * @param CatalogProductSimple $catalogProductSimple
     */
    public function testUpdateCatalogEvent(
        CatalogEventEntity $catalogEvent,
        CatalogProductSimple $catalogProductSimple
    ) {
        $filter = [
            'category_name' => $catalogProductSimple->getCategoryIds()[0]['name'],
        ];

        //Steps
        $this->catalogEventIndex->open();
        $this->catalogEventIndex->getBlockEventGrid()->searchAndOpen($filter);

        $this->catalogEventNew->getEventForm()->fill($catalogEvent);
        $this->catalogEventNew->getPageActions()->save();
    }
}
