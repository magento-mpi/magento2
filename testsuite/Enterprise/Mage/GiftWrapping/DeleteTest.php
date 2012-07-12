<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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

    /**
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Gift Wrapping</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_gift_wrapping');
    }

    /**
     * <p>Preconditions:</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Gift Wrapping" page;</p>
     * <p>2. Click button "Add Gift Wrapping";</p>
     * <p>3. Fill all required fields with correct data;</p>
     * <p>4. Save gift wrapping</p>
     * <p>Expected Results:</p>
     * <p>1. Gift wrapping is created;</p>
     *
     * @return string
     *
     * @test
     */
    public function createWrapping()
    {
        //Data
        $giftWrappingData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');

        return $giftWrappingData['gift_wrapping_design'];
    }

    /**
     * <p>Test Case Test Case TL-MAGE-867: Deleting Gift Wrapping</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Gift Wrapping" page;</p>
     * <p>2. Open previously created gift wrapping;</p>
     * <p>3. Press "Delete" button;</p>
     * <p>4. Cancel deletion;</p>
     * <p>5. Press "Delete" button;</p>
     * <p>6. Submit deletion;</p>
     * <p>Expected Results:</p>
     * <p>1. Gift wrapping is not deleted;</p>
     * <p>2. Gift wrapping is deleted;</p>
     *
     * @depends createWrapping
     * @param string $wrappingDesign
     *
     * @test
     */
    public function deleteWrapping($wrappingDesign)
    {
        //Data
        $giftWrappingSearch = $this->loadDataSet('GiftWrapping', 'search_gift_wrapping',
            array('filter_gift_wrapping_design' => $wrappingDesign));
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->deleteGiftWrapping($giftWrappingSearch, true);
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->deleteGiftWrapping($giftWrappingSearch);
        //Verification
        $this->assertMessagePresent('success', 'success_deleted_gift_wrapping');
    }

    /**
     * <p>Test Case Test Case TL-MAGE-877: Mass actions with Gift Wrappings (delete) </p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Gift Wrapping" page;</p>
     * <p>2. Select previously created gift wrapping by checking checkbox;</p>
     * <p>3. Choose massaction action "Delete";</p>
     * <p>4. Submit action;</p>
     * <p>5. Cancel action;</p>
     * <p>6. Select previously created gift wrapping by checking checkbox;</p>
     * <p>7. Choose massaction action "Delete";</p>
     * <p>8. Submit action;</p>
     * <p>Expected Results:</p>
     * <p>1. Gift wrapping is not deleted;</p>
     * <p>2. Gift wrapping is deleted;</p>
     *
     * @test
     */
    public function massactionDeleteWrapping()
    {
        //Preconditions
        $giftWrappingData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->searchAndChoose(array('filter_gift_wrapping_design' => $giftWrappingData['gift_wrapping_design']));
        $this->fillDropdown('massaction_action', 'Delete');
        $this->chooseCancelOnNextConfirmation();
        $this->clickButton('submit', false);
        $this->getConfirmation();
        $this->navigate('manage_gift_wrapping');
        $this->searchAndChoose(array('filter_gift_wrapping_design' => $giftWrappingData['gift_wrapping_design']));
        $this->fillDropdown('massaction_action', 'Delete');
        $this->addParameter('itemCount', '1');
        $this->saveForm('submit');
        $this->getConfirmation();
        //Verification
        $this->assertMessagePresent('success', 'success_massaction_delete');
    }
}
