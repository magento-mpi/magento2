<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CatalogEvent\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Fixture\CatalogProductSimple\CategoryIds;
use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogEventIndex;
use Magento\CatalogEvent\Test\Page\Adminhtml\CatalogEventNew;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Delete CatalogEventEntity
 *
 * Test Flow:
 * 1. Log in to backend as admin user.
 * 2. Navigate to MARKETING>Private Sales>Events.
 * 3. Choose catalog event from precondition.
 * 4. Click "Delete" button.
 * 5. Perform all assertions.
 *
 * @group Catalog_Events_(MX)
 * @ZephyrId MAGETWO-23418
 */
class DeleteCatalogEventEntityTest extends Injectable
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

        /** @var CatalogProductSimple $product */
        $product = $fixtureFactory->createByCode(
            'catalogProductSimple',
            ['dataSet' => 'product_with_category']
        );
        $product->persist();

        /** @var CategoryIds $sourceCategories */
        $sourceCategory = $product->getDataFieldConfig('category_ids')['source']->getCategories()[0];
        $catalogEventEntity = $fixtureFactory->createByCode(
            'catalogEventEntity',
            [
                'dataSet' => 'default_event',
                'data' => ['category_id' => $sourceCategory->getId()],
            ]
        );
        $catalogEventEntity->persist();

        return [
            'product' => $product,
            'catalogEventEntity' => $catalogEventEntity,
        ];
    }

    /**
     * Delete Catalog Event Entity
     *
     * @param CatalogProductSimple $product
     * @return void
     */
    public function testDeleteCatalogEvent(
        CatalogProductSimple $product
    ) {
        $filter = [
            'category_name' => $product->getCategoryIds()[0],
        ];

        //Steps
        $this->catalogEventIndex->open();
        $this->catalogEventIndex->getEventGrid()->searchAndOpen($filter);
        $this->catalogEventNew->getPageActions()->delete();
    }
}
