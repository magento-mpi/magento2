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
 * Delete Rating in Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Rating_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * Log in to Backend.
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * Preconditions: Create new store and simple product
     *
     * @test
     * @return string
     */
    public function preconditionsForTests()
    {
        //Data
        $simpleData = $this->loadDataSet('Product', 'simple_product_visible');
        $storeView = $this->loadDataSet('StoreView', 'generic_store_view');
        //Steps
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($storeView, 'store_view');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simpleData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'store' => $storeView['store_view_name'],
            'sku' => $simpleData['general_sku']
        );
    }

    /**
     * Delete rating that is used in Review
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3478
     */
    public function deleteRatingUsedInReview($data)
    {
        $rating = $this->loadDataSet('ReviewAndRating', 'default_rating', array('visible_in' => $data['store']));
        $review = $this->loadDataSet('ReviewAndRating', 'review_required_with_rating', array(
            'rating_name' => $rating['default_value'],
            'visible_in' => $data['store'],
            'filter_sku' => $data['sku']
        ));
        $searchRating = $this->loadDataSet('ReviewAndRating', 'search_rating',
            array('filter_rating_name' => $rating['default_value']));
        $searchReview = $this->loadDataSet('ReviewAndRating', 'search_review_admin', array(
            'filter_nickname' => $review['nickname'],
            'filter_product_sku' => $data['sku']
        ));
        //Steps
        $this->navigate('manage_ratings');
        $this->ratingHelper()->createRating($rating);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rating');
        //Steps
        $this->navigate('manage_all_reviews');
        $this->reviewHelper()->createReview($review);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_review');
        //Steps
        $this->navigate('manage_ratings');
        $this->ratingHelper()->deleteRating($searchRating);
        //Verification
        $this->assertMessagePresent('success', 'success_deleted_rating');
        //Steps
        $this->navigate('manage_all_reviews');
        $this->reviewHelper()->openReview($searchReview);
        //Verification
        $this->assertMessagePresent('success', 'not_available_rating');
    }

    /**
     * Delete rating
     *
     * @test
     * @TestlinkId TL-MAGE-3477
     */
    public function deleteRatingNotUsedInReview()
    {
        $rating = $this->loadDataSet('ReviewAndRating', 'rating_required_fields');
        $search = $this->loadDataSet('ReviewAndRating', 'search_rating',
            array('filter_rating_name' => $rating['default_value']));
        //Steps
        $this->navigate('manage_ratings');
        $this->ratingHelper()->createRating($rating);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rating');
        //Steps
        $this->ratingHelper()->deleteRating($search);
        //Verification
        $this->assertMessagePresent('success', 'success_deleted_rating');
    }
}
