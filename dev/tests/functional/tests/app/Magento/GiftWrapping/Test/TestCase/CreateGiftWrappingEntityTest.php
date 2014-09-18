<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\TestCase;

use Magento\GiftWrapping\Test\Fixture\GiftWrapping;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingIndex;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingNew;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Create GiftWrappingEntity
 *
 * Test Flow:
 * Steps:
 * 1. Login as admin to backend
 * 2. Navigate to Stores > Other Settings > Gift Wrapping
 * 3. Click the 'Add Gift Wrapping' button
 * 4. Fill out fields from data set
 * 5. Click 'Save' button
 * 6. Perform asserts
 *
 * @group Gift_Wrapping_(CS)
 * @ZephyrId MAGETWO-24797
 */
class CreateGiftWrappingEntityTest extends Injectable
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
     * Create Gift Wrapping entity test
     *
     * @param GiftWrapping $giftWrapping
     * @return void
     */
    public function test(GiftWrapping $giftWrapping)
    {
        // Steps
        $this->giftWrappingIndexPage->open();
        $this->giftWrappingIndexPage->getGridPageActions()->addNew();
        $this->giftWrappingNewPage->getGiftWrappingForm()->fill($giftWrapping);
        $this->giftWrappingNewPage->getFormPageActions()->save();
    }
}
