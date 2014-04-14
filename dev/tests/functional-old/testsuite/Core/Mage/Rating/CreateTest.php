<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Rating
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating creation into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Rating_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Log in to Backend.</p>
     */
    public function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     *
     * @test
     * @return string
     */
    public function preconditionsForTests()
    {
        //Data
        $storeView = $this->loadDataSet('StoreView', 'generic_store_view');
        //Steps
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($storeView, 'store_view');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_store_view');

        return $storeView['store_view_name'];
    }

    /**
     * <p>Creating Rating with required fields only</p>
     *
     * @return array
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3474
     */
    public function withRequiredFieldsOnly()
    {
        //Data
        $ratingData = $this->loadDataSet('ReviewAndRating', 'rating_required_fields');
        //Steps
        $this->navigate('manage_ratings');
        $this->ratingHelper()->createRating($ratingData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rating');

        return $ratingData;
    }

    /**
     * <p>Creating Rating with empty required fields</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3470
     */
    public function withEmptyDefaultValue()
    {
        //Data
        $rating =
            $this->loadDataSet('ReviewAndRating', 'rating_required_fields', array('default_value' => '%noValue%'));
        //Steps
        $this->navigate('manage_ratings');
        $this->ratingHelper()->createRating($rating);
        //Verification
        $this->addFieldIdToMessage('field', 'default_value');
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating Rating with existing name(default value)</p>
     *
     * @param $ratingData
     *
     * @test
     * @depends withRequiredFieldsOnly
     * @TestlinkId TL-MAGE-3471
     */
    public function withExistingRatingName($ratingData)
    {
        //Steps
        $this->navigate('manage_ratings');
        $this->ratingHelper()->createRating($ratingData);
        //Verification
        $this->assertMessagePresent('error', 'existing_name');
    }

    /**
     * <p>Creating Rating with filling Fields</p>
     *
     * @param $storeView
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-435
     */
    public function withAllFields($storeView)
    {
        $rating = $this->loadDataSet('ReviewAndRating', 'default_rating', array('visible_in' => $storeView));
        $search = $this->loadDataSet('ReviewAndRating', 'search_rating',
            array('filter_rating_name' => $rating['default_value']));
        //Steps
        $this->navigate('manage_ratings');
        $this->ratingHelper()->createRating($rating);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rating');
        //Steps
        $this->ratingHelper()->openRating($search);
        $this->ratingHelper()->verifyRatingData($rating);
    }

    /**
     * <p>Creating a new rating with long values into required fields</p>
     *
     * @param $storeView
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3473
     */
    public function withLongValues($storeView)
    {
        $rating = $this->loadDataSet('ReviewAndRating', 'rating_long_values', array('visible_in' => $storeView));
        $search = $this->loadDataSet('ReviewAndRating', 'search_rating',
            array('filter_rating_name' => $rating['default_value']));
        //Steps
        $this->navigate('manage_ratings');
        $this->ratingHelper()->createRating($rating);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rating');
        //Steps
        $this->ratingHelper()->openRating($search);
        $this->ratingHelper()->verifyRatingData($rating);
        $this->clickButtonAndConfirm('delete_rating', 'confirmation_for_single_delete');
    }

    /**
     * <p>Creating a new rating with special characters into required fields</p>
     *
     * @param $storeView
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3475
     */
    public function withSpecialCharacters($storeView)
    {
        $rating = $this->loadDataSet('ReviewAndRating', 'rating_special_symbols', array('visible_in' => $storeView));
        $search = $this->loadDataSet('ReviewAndRating', 'search_rating',
            array('filter_rating_name' => $rating['default_value']));
        //Steps
        $this->navigate('manage_ratings');
        $this->ratingHelper()->createRating($rating);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rating');
        //Steps
        $this->ratingHelper()->openRating($search);
        $this->ratingHelper()->verifyRatingData($rating);
        $this->clickButtonAndConfirm('delete_rating', 'confirmation_for_single_delete');
    }
}