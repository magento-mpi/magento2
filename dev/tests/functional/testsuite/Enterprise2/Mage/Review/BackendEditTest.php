<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Review
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Edit review into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_Review_BackendEditTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Login as admin to backend</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     * <p>Product created</p>
     *
     * @test
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $simpleData = $this->loadDataSet('Product', 'simple_product_visible');
        $storeView = $this->loadDataSet('StoreView', 'generic_store_view');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simpleData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('sku'        => $simpleData['general_sku'], 'name' => $simpleData['general_name'],
                     'store'      => $storeView['store_view_name'],
                     'withRating' => array('filter_sku'  => $simpleData['general_sku'],
                                           'visible_in'  => $storeView['store_view_name']));
    }

    /**
     * <p>Check Prev and Next buttons while editing</p>
     *
     * <p>Preconditions:</p>
     * <p>Two reviews created</p>
     *
     * <p>Expected result:</p>
     * <p>"Next" and "Previous" buttons must not save changes and rotate reviews in borders of resultset</p>
     * <p>"Save and Next" and "Save and Previous" buttons must save changes and rotate reviews in borders of
     * resultset</p>
     *
     * @param $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5849
     * @TestlinkId TL-MAGE-5850
     */
    public function checkPrevAndNextButtons($data)
    {
        //Data
        $reviewDataFirst = $this->loadDataSet('ReviewAndRating', 'review_required_without_rating',
            array('filter_sku' => $data['sku']));
        $reviewDataSecond = $this->loadDataSet('ReviewAndRating', 'review_required_without_rating',
            array('filter_sku' => $data['sku']));
        $search = $this->loadDataSet('ReviewAndRating', 'search_review_admin',
            array('filter_product_sku' => $data['sku']));

        //Steps
        $this->navigate('manage_all_reviews');
        $this->reviewHelper()->createReview($reviewDataFirst);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_review');

        $this->reviewHelper()->createReview($reviewDataSecond);
        $this->assertMessagePresent('success', 'success_saved_review');


        //Steps
        $this->reviewHelper()->fillSearchFormAndOpenReview($search);
        //Verification
        $this->assertTrue($this->buttonIsPresent('next_review'), 'There is no "Next" button on the page');
        $this->assertTrue($this->buttonIsPresent('next_save_review'),
            'There is no "Save and Next" button on the page');
        $this->assertFalse($this->buttonIsPresent('prev_review'),
            'There is present "Previous" button on the page');
        $this->assertFalse($this->buttonIsPresent('prev_save_review'),
            'There is present "Save and Previous" button on the page');

        $fieldReviewTextXpath = $this->_getControlXpath('field', 'review');

        // Check 'Next' and 'Prev' buttons don't save changes and move to other reviews
        $this->fillField('review', 'test text');
        $this->clickButton('next_review');
        $this->assertMessageNotPresent('success', 'success_saved_review');
        $this->assertFalse($this->buttonIsPresent('next_review'),
            'There is present "Next" button on the page');
        $this->assertFalse($this->buttonIsPresent('next_save_review'),
            'There is present "Next and Previous" button on the page');

        // Check 'Next and Save' and 'Prev and Save' buttons save changes
        $this->fillField('review', 'test text');
        $this->clickButton('prev_review');
        $this->assertMessageNotPresent('success', 'success_saved_review');

        $this->assertElementValueEquals($fieldReviewTextXpath, $reviewDataSecond['review']);

        $this->clickButton('next_review');
        $this->assertMessageNotPresent('success', 'success_saved_review');
        $this->assertElementValueEquals($fieldReviewTextXpath, $reviewDataFirst['review']);

        $this->fillField('review', 'test text');
        $this->clickButton('prev_save_review');
        $this->assertMessagePresent('success', 'success_saved_review');

        $this->fillField('review', 'test text');
        $this->clickButton('next_save_review');
        $this->assertMessagePresent('success', 'success_saved_review');
        $this->assertElementValueEquals($fieldReviewTextXpath, 'test text');

        $this->clickButton('prev_review');
        $this->assertMessageNotPresent('success', 'success_saved_review');
        $this->assertElementValueEquals($fieldReviewTextXpath, 'test text');
    }

    /**
     * <p>Behavior of the "Next" and "Previous" navigation when filtered field is changing</p>
     *
     * <p>Preconditions:</p>
     * <p>Review created</p>
     *
     * <p>Expected result:</p>
     * <p>Consist of rotation resultset must not be changed after changes in filtered field</p>
     *
     * @param $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5851
     */
    public function checkPrevAndNextButtonsFilterChanged($data)
    {
        //Data
        $someRandomReviewText = 'Some text for TL-MAGE-5851 ' . $this->generate('string', 5, ':lower:');
        $reviewData = $this->loadDataSet('ReviewAndRating', 'review_required_without_rating',
            array('filter_sku' => $data['sku'], 'review' => $someRandomReviewText));
        $reviewData2 = $this->loadDataSet('ReviewAndRating', 'review_required_without_rating',
            array('filter_sku' => $data['sku'], 'review' => $someRandomReviewText));
        $search = $this->loadDataSet('ReviewAndRating', 'search_review_admin',
            array( 'filter_review' => $someRandomReviewText));

        //Steps
        $this->navigate('manage_all_reviews');
        $this->reviewHelper()->createReview($reviewData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_review');

        $this->reviewHelper()->createReview($reviewData2);
        $this->assertMessagePresent('success', 'success_saved_review');

        //Steps
        $this->reviewHelper()->fillSearchFormAndOpenReview($search);
        //Verification
        $this->assertTrue($this->buttonIsPresent('next_review'), 'There is no "Next" button on the page');
        $this->assertTrue($this->buttonIsPresent('next_save_review'),
            'There is no "Save and Next" button on the page');
        $this->assertFalse($this->buttonIsPresent('prev_review'),
            'There is present "Previous" button on the page');
        $this->assertFalse($this->buttonIsPresent('prev_save_review'),
            'There is present "Save and Previous" button on the page');

        $this->fillField('review', 'test text');
        $this->clickButton('next_save_review');
        $this->assertMessagePresent('success', 'success_saved_review');
        $this->assertTrue($this->buttonIsPresent('prev_review'),
            'There is absent "Previous" button on the page');
        $this->assertTrue($this->buttonIsPresent('prev_save_review'),
            'There is absent "Save and Previous" button on the page');

        $this->clickButton('prev_review');
        $this->assertMessageNotPresent('success', 'success_saved_review');
        $this->assertTrue($this->buttonIsPresent('next_review'), 'There is no "Next" button on the page');
        $this->assertTrue($this->buttonIsPresent('next_save_review'),
            'There is no "Save and Next" button on the page');
    }

}
