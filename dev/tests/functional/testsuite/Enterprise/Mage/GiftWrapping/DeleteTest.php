<?php

/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GiftWrapping
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Deletion of gift wrapping
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_GiftWrapping_DeleteTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_gift_wrapping');
    }

    /**
     * <p>Test Case Test Case TL-MAGE-867: Deleting Gift Wrapping</p>
     *
     * @test
     */
    public function deleteWrapping()
    {
        //Data
        $giftWrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $search = $this->loadDataSet('GiftWrapping', 'search_gift_wrapping',
            array('filter_gift_wrapping_design' => $giftWrapping['gift_wrapping_design']));
        //Steps
        $this->giftWrappingHelper()->createGiftWrapping($giftWrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->deleteGiftWrapping($search, true);
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->deleteGiftWrapping($search);
        //Verification
        $this->assertMessagePresent('success', 'success_deleted_gift_wrapping');
    }

    /**
     * <p>Test Case Test Case TL-MAGE-877: Mass actions with Gift Wrappings (delete) </p>
     *
     * @test
     */
    public function massactionDeleteWrapping()
    {
        //Data
        $giftWrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $search = $this->loadDataSet('GiftWrapping', 'search_gift_wrapping',
            array('filter_gift_wrapping_design' => $giftWrapping['gift_wrapping_design']));
        $this->addParameter('itemCount', '1');
        //Steps
        $this->giftWrappingHelper()->createGiftWrapping($giftWrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->navigate('manage_gift_wrapping');
        $this->searchAndChoose($search, 'gift_wrapping_grid');
        $this->fillDropdown('massaction_action', 'Delete');
        $this->clickButton('submit', false);
        $this->dismissAlert();
        $this->searchAndChoose($search, 'gift_wrapping_grid');
        $this->fillDropdown('massaction_action', 'Delete');
        $this->clickControlAndConfirm('button', 'submit', 'massaction_confirmation_for_delete');
        //Verification
        $this->assertMessagePresent('success', 'success_massaction_delete');
    }
}
