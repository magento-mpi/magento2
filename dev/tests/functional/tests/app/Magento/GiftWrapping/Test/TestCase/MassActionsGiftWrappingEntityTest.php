<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\GiftWrapping\Test\Fixture\GiftWrapping;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingNew;
use Magento\GiftWrapping\Test\Page\Adminhtml\GiftWrappingIndex;

/**
 * Test Creation for MassActionsGiftWrappingEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Gift Wrapping entities should be created according to each row in DS
 *
 * Steps:
 * 1. Login as admin to backend
 * 2. Navigate to Stores > Other Settings > Gift Wrapping
 * 3. Select gift wrappers according to DS
 * 4. Select a mass action according to DS and execute any depended operation if any
 * 5. Click 'Submit' button
 * 6. Perform all asserts
 *
 * @group Gift_Wrapping_(CS)
 * @ZephyrId MAGETWO-27896
 */
class MassActionsGiftWrappingEntityTest extends Injectable
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
     * Mass actions for Gift Wrapping entity test
     *
     * @param string $giftWrappings
     * @param FixtureFactory $fixtureFactory
     * @param string $giftWrappingsIndexToSelect
     * @param string $action
     * @param string $status
     * @param string $giftWrappingsIndexToStay
     * @return array
     */
    public function test(
        $giftWrappings,
        FixtureFactory $fixtureFactory,
        $giftWrappingsIndexToSelect,
        $action,
        $status,
        $giftWrappingsIndexToStay
    ) {
        // Precondition
        $elements = explode(",", $giftWrappings);
        $giftWrappings = [];
        foreach ($elements as $giftWrapping) {
            $giftWrapping = $fixtureFactory->createByCode('giftWrapping', ['dataSet' => $giftWrapping]);
            $giftWrapping->persist();
            $giftWrappings[] = $giftWrapping;
        }

        // Steps
        $giftWrappingsIndexToSelect = explode(",", $giftWrappingsIndexToSelect);
        $giftWrappingsToSelect = [];
        $giftWrappingsToModify = [];
        foreach ($giftWrappingsIndexToSelect as $giftWrappingIndex) {
            $giftWrappingsToSelect[] = ['design' => $giftWrappings[$giftWrappingIndex-1]->getDesign()];
            $giftWrappingsToModify[] = $giftWrappings[$giftWrappingIndex-1];
        }
        $giftWrappingsIndexToStay = explode(",", $giftWrappingsIndexToStay);
        $giftWrappingsToStay = [];
        foreach ($giftWrappingsIndexToStay as $giftWrappingIndex) {
            $giftWrappingsToStay[] = $giftWrappingIndex !== '-' ? $giftWrappings[$giftWrappingIndex-1] : null;
        }
        $this->giftWrappingIndexPage->open();
        $this->giftWrappingIndexPage->getGiftWrappingGrid()->massaction(
            $giftWrappingsToSelect,
            [$action => $status],
            ($action == 'Delete' ? true : false)
        );

        return [
            'giftWrappingsModified' => $giftWrappingsToModify,
            'giftWrappings' => $giftWrappingsToStay,
        ];
    }
}
