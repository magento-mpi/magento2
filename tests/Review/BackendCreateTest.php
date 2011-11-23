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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tests for Review verification in Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Review_CreateTest extends Mage_Selenium_TestCase
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
     * <p>Navigate to Catalog -> Reviews and Ratings -> Customer Reviews -> All Reviews</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('all_reviews');
        $this->addParameter('storeId', '1');
    }

    /**
     * <p>Creating a new review</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Review"</p>
     * <p>2. Select product from the grid</p>
     * <p>3. Fill in fields in Review Details area</p>
     * <p>4. Click button "Save Review"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the review has been saved.</p>
     *
     * @test
     */
    public function withRequiredFieldsOnly()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Creating a new review when its visible in other Store View with Rating</p>
     * <p>Preconditions:</p>
     * <p>Rating created</p>
     * <p>Store view created</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Review"</p>
     * <p>2. Select product from the grid</p>
     * <p>3. Fill in fields in Review Details area - select desired Store View and specify Rating</p>
     * <p>4. Click button "Save Review"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the review has been saved.</p>
     *
     * <p>Verification:</p>
     * <p>1. Login to frontend;</p>
     * <p>2. Verify that review is absent in Category and product Page in Default Store View;</p>
     * <p>3. Switch to correct store view;</p>
     * <p>4. Verify review in category;</p>
     * <p>5. Verify review on product page;</p>
     *
     * @test
     */
    public function test_WithRequiredFieldsRatingAndVisibleIn()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Creating a new review with Rating</p>
     * <p>Preconditions:</p>
     * <p>Rating created</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Review"</p>
     * <p>2. Select product from the grid</p>
     * <p>3. Fill in fields in Review Details area - specify Rating</p>
     * <p>4. Click button "Save Review"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the review has been saved.</p>
     *
     * @test
     */
    public function withRequiredFieldsWithRating()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Creating a new review visible in other store view</p>
     * <p>Preconditions:</p>
     * <p>Store View created</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Review"</p>
     * <p>2. Select product from the grid</p>
     * <p>3. Fill in fields in Review Details area - specify Rating</p>
     * <p>4. Click button "Save Review"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the review has been saved.</p>
     *
     * <p>Verification:</p>
     * <p>1. Login to frontend;</p>
     * <p>2. Verify that review is absent in Category and product Page in Default Store View;</p>
     * <p>3. Switch to correct store view;</p>
     * <p>4. Verify review in category;</p>
     * <p>5. Verify review on product page;</p>
     *
     * @test
     */
    public function withRequiredFieldsWithVisibleIn()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Empty fields validation</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Review"</p>
     * <p>2. Select product from the grid</p>
     * <p>3. Leave fields in Review Details area empty</p>
     * <p>4. Click button "Save Review"</p>
     * <p>Expected result:</p>
     * <p>Error message appears</p>
     *
     * @test
     */
    public function withRequiredFieldsEmpty()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Creating a new review with long values into required fields</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Review"</p>
     * <p>2. Select product from the grid</p>
     * <p>3. Fill in fields in Review Details area by long values</p>
     * <p>4. Click button "Save Review"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the review has been saved.</p>
     *
     * @test
     */
    public function withLongValues()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Creating a new review with incorrect length into required fields</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Review"</p>
     * <p>2. Select product from the grid</p>
     * <p>3. Fill in fields in Review Details area by long values</p>
     * <p>4. Click button "Save Review"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the review has been saved.</p>
     *
     * @test
     */
    public function withIncorrectLengthInRequiredFields()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p>Creating a new review with special characters into required fields</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Review"</p>
     * <p>2. Select product from the grid</p>
     * <p>3. Fill in fields in Review Details area by special characters</p>
     * <p>4. Click button "Save Review"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the review has been saved.</p>
     *
     * @test
     */
    public function withSpecialCharacters()
    {
        $this->markTestIncomplete('@TODO');
    }

    /**
     * <p> Update review status</p>
     *
     * <p>Preconditions:</p>
     * <p>Review with required fields created and status - "Pending";</p>
     *
     * <p>Steps</p>
     * <p>1. Select created review from the list at "All Reviews" page (by checking checkbox);</p>
     * <p>2. Select in "Actions" - "Update Status";</p>
     * <p>3. Update status to "Approved";</p>
     * <p>4. Click "Submit" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - status updated</p>
     */
    public function test_ChangeStatusOfReview()
    {
        $this->markTestIncomplete('@TODO');
    }

}
