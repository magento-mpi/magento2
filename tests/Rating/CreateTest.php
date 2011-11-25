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
 * Rating creation into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Rating_CreateTest extends Mage_Selenium_TestCase
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
     * <p>Navigate to Catalog -> Reviews and Ratings -> Manage Ratings</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_ratings');
        //$this->addParameter('storeId', '1');
    }


    /**
     * <p>Creating Rating with required fields only</p>
     *
     * <p>Steps:</p>
     * <p>1. Click "Add New Rating" button;</p>
     * <p>2. Fill in required fields by regular data;</p>
     * <p>3. Click "Save Rating" button;</p>
     * <p>Expected result:</p>
     * <p>Success message appears - rating saved</p>
     *
     * @test
     */
    public function withRequiredFieldsOnly()
    {
        $ratingData = $this->loadData('rating_required_fields', NULL, 'default_value');
        $this->ratingHelper()->createRating($ratingData);
        $this->assertTrue($this->successMessage('success_saved_rating'), $this->messages);

        return $ratingData['rating_information']['default_value'];
    }

    /**
     * <p>Creating Rating with empty required fields</p>
     *
     * <p>Steps:</p>
     * <p>1. Click "Add New Rating" button;</p>
     * <p>2. Leave required fields empty;</p>
     * <p>3. Click "Save Rating" button;</p>
     * <p>Expected result:</p>
     * <p>Error message appears - "This is a required field";</p>
     *
     * @test
     */
    public function withEmptyDefaultValue()
    {
        $ratingData = $this->loadData('rating_required_fields', array('default_value' => '%noValue%'));
        $this->ratingHelper()->createRating($ratingData);
        $this->addFieldIdToMessage('field', 'default_value');
        $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }


    /**
     * <p>Creating Rating with existing name(default value)</p>
     *
     * <p>Steps:</p>
     * <p>1. Click "Add New Rating" button;</p>
     * <p>2. Fill in "Default Value" with existing value;</p>
     * <p>3. Click "Save Rating" button;</p>
     * <p>Expected result:</p>
     * <p>Rating is not saved, Message appears "already exists."</p>
     *
     * @depends withRequiredFieldsOnly
     * @test
     */
    public function withExistingRatingName($ratingName)
    {
        $ratingData = $this->loadData('rating_required_fields', array('default_value' => $ratingName));
        $this->ratingHelper()->createRating($ratingData);
        $this->assertTrue($this->errorMessage('existing_name'), $this->messages);
    }

//    /**
//     * <p>Creating Rating with Visible In option</p>
//     *
//     * <p>Preconditions:</p>
//     * <p>Store View created</p>
//     * <p>Product created</p>
//     * <p>Steps:</p>
//     * <p>1. Click "Add New Rating" button;</p>
//     * <p>2. Fill in necessary fields by regular data - select created store view into "Visible In" block;</p>
//     * <p>3. Click "Save Rating" button;</p>
//     * <p>Expected result:</p>
//     * <p>Success message appears - rating saved</p>
//     *
//     * <p>Verification:</p>
//     * <p>Goto Frontend;</p>
//     * <p>Open created Product</p>
//     * <p>Verify that rating is absent on Product Page;</p>
//     * <p>Switch to created Store View;</p>
//     * <p>Navigate to Product Page</p>
//     * <p>Verify that rating is present on product page</p>
//     *
//     * @test
//     */
//    public function withVisibleIn()
//    {
//        $this->markTestIncomplete('@TODO');
//    }

    /**
     * <p>Creating a new rating with long values into required fields</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Rating"</p>
     * <p>2. Fill in fields in Rating Details area by long values</p>
     * <p>4. Click button "Save Rating"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the rating has been saved.</p>
     *
     * @test
     */
    public function withLongValues()
    {
        $ratingData = $this->loadData('rating_required_fields',
                                      array('default_value' => $this->generate('string', 64, ':alnum:')));
        $searchData = $this->loadData('search_rating',
                                      array('filter_rating_name' =>$ratingData['rating_information']['default_value']));
        $this->ratingHelper()->createRating($ratingData);
        $this->assertTrue($this->successMessage('success_saved_rating'), $this->messages);
        $this->ratingHelper()->openRating($searchData);
    }

    /**
     * <p>Creating a new rating with incorrect length into required fields</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Rating"</p>
     * <p>2. Fill in fields in Rating Details area by long values</p>
     * <p>4. Click button "Save Rating"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the rating has been saved.</p>
     *
     * @test
     */
    public function withIncorrectLengthInRequiredFields()
    {
        $ratingData = $this->loadData('rating_required_fields',
                                      array('default_value' => $this->generate('string', 65, ':alnum:')));
        $searchData = $this->loadData('search_rating',
                                      array('filter_rating_name' =>
                                      substr($ratingData['rating_information']['default_value'],0,-1)));
        $this->ratingHelper()->createRating($ratingData);
        $this->assertTrue($this->successMessage('success_saved_rating'), $this->messages);
        $this->ratingHelper()->openRating($searchData);
    }

    /**
     * <p>Creating a new rating with special characters into required fields</p>
     * <p>Steps:</p>
     * <p>1. Click button "Add New Rating"</p>
     * <p>2. Fill in fields in Review Details area by special characters</p>
     * <p>3. Click button "Save Rating"</p>
     * <p>Expected result:</p>
     * <p>Received the message that the rating has been saved.</p>
     *
     * @test
     */
    public function withSpecialCharacters()
    {
        $ratingData = $this->loadData('rating_required_fields',
                                      array('default_value' => $this->generate('string', 32, ':punct:')));
        $searchData = $this->loadData('search_rating',
                                      array('filter_rating_name' =>$ratingData['rating_information']['default_value']));
        $this->ratingHelper()->createRating($ratingData);
        $this->assertTrue($this->successMessage('success_saved_rating'), $this->messages);
        $this->ratingHelper()->openRating($searchData);
    }

}
