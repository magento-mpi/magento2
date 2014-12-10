<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftWrapping\Test\TestCase;

use Magento\GiftWrapping\Test\Fixture\GiftWrapping;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingIndex;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingNew;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for DeleteGiftWrappingEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Gift Wrapping is created.
 *
 * Steps:
 * 1. Login as admin to backend
 * 2. Navigate to Stores > Other Settings > Gift Wrapping
 * 3. Open created Gift Wrapping
 * 4. Click 'Delete' button
 * 5. Perform all assertions
 *
 * @group Gift_Wrapping_(CS)
 * @ZephyrId MAGETWO-27659
 */
class DeleteGiftWrappingEntityTest extends Injectable
{
    /**
     * Gift Wrapping grid page
     *
     * @var GiftWrappingIndex
     */
    protected $giftWrappingIndexPage;

    /**
     * Gift Wrapping new/edit page
     *
     * @var GiftWrappingNew
     */
    protected $giftWrappingNewPage;

    /**
     * Injection data
     *
     * @param GiftWrappingIndex $giftWrappingIndexPage
     * @param GiftWrappingNew $giftWrappingNewPage
     * @return void
     */
    public function __inject(GiftWrappingIndex $giftWrappingIndexPage, GiftWrappingNew $giftWrappingNewPage)
    {
        $this->giftWrappingIndexPage = $giftWrappingIndexPage;
        $this->giftWrappingNewPage = $giftWrappingNewPage;
    }

    /**
     * Delete Gift Wrapping Entity test
     *
     * @param GiftWrapping $giftWrapping
     * @return void
     */
    public function test(GiftWrapping $giftWrapping)
    {
        $this->markTestIncomplete('Bug: MAGETWO-30995');
        // Precondition
        $giftWrapping->persist();

        // Steps
        $filter = ['design' => $giftWrapping->getDesign()];
        $this->giftWrappingIndexPage->open();
        $this->giftWrappingIndexPage->getGiftWrappingGrid()->searchAndOpen($filter);
        $this->giftWrappingNewPage->getFormPageActions()->delete();
    }
}
