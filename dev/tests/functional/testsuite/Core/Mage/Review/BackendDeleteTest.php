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
 * Delete review into backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Review_BackendDeleteTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Login as admin to backend</p>
     * <p>Navigate to Catalog -> Reviews and Ratings -> Customer Reviews -> All Reviews</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Preconditions:</p>
     *
     * @test
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $simpleData = $this->loadDataSet('Product', 'simple_product_visible');
        $storeView = $this->loadDataSet('StoreView', 'generic_store_view');
        $ratingData = $this->loadDataSet('ReviewAndRating', 'default_rating',
            array('visible_in' => $storeView['store_view_name']));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simpleData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($storeView, 'store_view');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Steps
        $this->navigate('manage_ratings');
        $this->ratingHelper()->createRating($ratingData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rating');
        return array('sku'        => $simpleData['general_sku'], 'name' => $simpleData['general_name'],
                     'store'      => $storeView['store_view_name'],
                     'withRating' => array('filter_sku'  => $simpleData['general_sku'],
                                           'rating_name' => $ratingData['default_value'],
                                           'visible_in'  => $storeView['store_view_name']));
    }

    /**
     * <p>Delete review with Rating</p>
     *
     * @param $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3600
     */
    public function deleteWithRating($data)
    {
        //Data
        $reviewData = $this->loadDataSet('ReviewAndRating', 'review_required_with_rating', $data['withRating']);
        $search = $this->loadDataSet('ReviewAndRating', 'search_review_admin',
            array('filter_nickname' => $reviewData['nickname'], 'filter_product_sku' => $data['sku']));
        //Steps
        $this->navigate('manage_all_reviews');
        $this->reviewHelper()->createReview($reviewData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_review');
        //Steps
        $this->reviewHelper()->deleteReview($search);
        //Verification
        $this->assertMessagePresent('success', 'success_deleted_review');
    }

    /**
     * <p>Delete review without Rating</p>
     *
     * @param $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-461
     */
    public function deleteWithoutRating($data)
    {
        //Data
        $reviewData = $this->loadDataSet('ReviewAndRating', 'review_required_without_rating',
            array('filter_sku' => $data['sku']));
        $search = $this->loadDataSet('ReviewAndRating', 'search_review_admin',
            array('filter_nickname' => $reviewData['nickname'], 'filter_product_sku' => $data['sku']));
        //Steps
        $this->navigate('manage_all_reviews');
        $this->reviewHelper()->createReview($reviewData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_review');
        //Steps
        $this->reviewHelper()->deleteReview($search);
        //Verification
        $this->assertMessagePresent('success', 'success_deleted_review');
    }

    /**
     * <p>Delete review using Mass-Action</p>
     *
     * @param $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-459
     */
    public function deleteMassAction($data)
    {
        //Data
        $reviewData = $this->loadDataSet('ReviewAndRating', 'review_required_without_rating',
            array('filter_sku' => $data['sku']));
        $search = $this->loadDataSet('ReviewAndRating', 'search_review_admin',
            array('filter_nickname' => $reviewData['nickname'], 'filter_product_sku' => $data['sku']));
        //Steps
        $this->navigate('manage_all_reviews');
        $this->reviewHelper()->createReview($reviewData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_review');
        //Steps
        $this->searchAndChoose($search, 'all_reviews_grid');
        $this->fillDropdown('mass_action_select_action', 'Delete');
        $this->clickButtonAndConfirm('submit', 'confirmation_for_delete');
        //Verification
        $this->assertMessagePresent('success', 'success_deleted_review_massaction');
    }
}