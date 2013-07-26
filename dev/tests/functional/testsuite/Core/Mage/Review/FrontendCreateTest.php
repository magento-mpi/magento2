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
 * Reviews Validation on the frontend
 *
 * @package selenium
 * @subpackage tests
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Review_FrontendCreateTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $config = $this->loadDataSet('FlatCatalog', 'flat_catalog_reviews',
            array('allow_guests_to_write_reviews' => 'Yes'));
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    protected function tearDownAfterTest()
    {
        $this->frontend();
        $this->selectFrontStoreView();
    }

    /**
     * <p>Preconditions</p>
     *
     * @test
     * @return array
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $storeView = $this->loadDataSet('StoreView', 'generic_store_view');
        $rating = $this->loadDataSet('ReviewAndRating', 'default_rating',
            array('visible_in' => $storeView['store_view_name']));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->navigate('manage_stores');
        $this->storeHelper()->createStore($storeView, 'store_view');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_store_view');
        //Steps
        $this->navigate('manage_ratings');
        $this->ratingHelper()->createRating($rating);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_rating');
        $this->reindexInvalidedData();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        $this->logoutCustomer();

        return array(
            'login' => array('email' => $userData['email'], 'password' => $userData['password']),
            'sku' => $simple['general_sku'],
            'name' => $simple['general_name'],
            'store' => $storeView['store_view_name'],
            'withRating' => array('filter_sku' => $simple['general_sku'], 'rating_name' => $rating['default_value'])
        );
    }

    /**
     * <p>Adding Review to product with Not Logged Customer<p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-440
     * @skipTearDown
     */
    public function addReviewByGuest($data)
    {
        //Data
        $review = $this->loadDataSet('ReviewAndRating', 'frontend_review');
        $searchData = $this->loadDataSet('ReviewAndRating', 'search_review_guest',
            array('filter_nickname' => $review['nickname'], 'filter_product_sku' => $data['name']));
        //Steps
        $this->logoutCustomer();
        $this->productHelper()->frontOpenProduct($data['name']);
        $this->reviewHelper()->frontendAddReview($review);
        //Verification
        $this->assertMessagePresent('success', 'accepted_review');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_all_reviews');
        $this->reviewHelper()->openReview($searchData);
        //Verification
        $this->reviewHelper()->verifyReviewData($review);
    }

    /**
     * <p>Adding Review with raring to product with Not Logged Customer<p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-457
     */
    public function addReviewByGuestWithRating($data)
    {
        $this->markTestIncomplete('BUG: Fatal error on page after save review');
        //Data
        $review = $this->loadDataSet('ReviewAndRating', 'review_with_rating',
            array('rating_name' => $data['withRating']['rating_name']));
        $searchData = $this->loadDataSet('ReviewAndRating', 'search_review_guest',
            array('filter_nickname' => $review['nickname'], 'filter_product_sku' => $data['name']));
        //Steps
        $this->logoutCustomer();
        $this->selectFrontStoreView($data['store']);
        $this->productHelper()->frontOpenProduct($data['name']);
        $this->reviewHelper()->frontendAddReview($review);
        //Verification
        $this->assertMessagePresent('success', 'accepted_review');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_all_reviews');
        $this->reviewHelper()->openReview($searchData);
        //Verification
        $this->reviewHelper()->verifyReviewData($review);
    }

    /**
     * <p>Review creating with Logged Customer</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-456
     * @skipTearDown
     */
    public function addReviewByLoggedCustomer($data)
    {
        //Data
        $simple = $data['name'];
        $reviewData = $this->loadDataSet('ReviewAndRating', 'frontend_review');
        $searchData = $this->loadDataSet('ReviewAndRating', 'search_review_customer',
            array('filter_nickname' => $reviewData['nickname'], 'filter_product_sku' => $simple));
        //Steps
        $this->customerHelper()->frontLoginCustomer($data['login']);
        $this->productHelper()->frontOpenProduct($simple);
        $this->reviewHelper()->frontendAddReview($reviewData);
        //Verification
        $this->assertMessagePresent('success', 'accepted_review');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_all_reviews');
        $this->reviewHelper()->editReview(array('status' => 'Approved'), $searchData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_review');
        $this->clearInvalidedCache();
        //Steps
        $this->productHelper()->frontOpenProduct($simple);
        //Verification
        $this->reviewHelper()->frontVerifyReviewDisplaying($reviewData, $simple);
        $this->reviewHelper()->frontVerifyReviewDisplayingInMyAccount($reviewData, $simple);
    }

    /**
     * <p>Review creating empty fields</p>
     *
     * @param string $emptyFieldName
     * @param array $data
     *
     * @test
     * @dataProvider withEmptyRequiredFieldsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3568
     * @skipTearDown
     */
    public function withEmptyRequiredFields($emptyFieldName, $data)
    {
        //Data
        $reviewData = $this->loadDataSet('ReviewAndRating', 'frontend_review', array($emptyFieldName => ''));
        //Steps
        $this->customerHelper()->logoutCustomer();
        $this->productHelper()->frontOpenProduct($data['name']);
        $this->reviewHelper()->frontendAddReview($reviewData);
        //Verification
        $this->addFieldIdToMessage('field', $emptyFieldName);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withEmptyRequiredFieldsDataProvider()
    {
        return array(
            array('nickname'),
            array('summary_of_review'),
            array('review')
        );
    }

    /**
     * <p>Review creating with Logged Customer with special characters in fields</p>
     *
     * @param string $reviewData
     * @param array $data
     *
     * @test
     * @dataProvider frontendReviewSpecialCharactersDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3569
     * @skipTearDown
     */
    public function frontendReviewSpecialCharacters($reviewData, $data)
    {
        //Data
        $reviewData = $this->loadDataSet('ReviewAndRating', $reviewData);
        $searchData = $this->loadDataSet('ReviewAndRating', 'search_review_guest',
            array('filter_nickname' => $reviewData['nickname'], 'filter_product_sku' => $data['name']));
        //Steps
        $this->logoutCustomer();
        $this->productHelper()->frontOpenProduct($data['name']);
        $this->reviewHelper()->frontendAddReview($reviewData);
        //Verification
        $this->assertMessagePresent('success', 'accepted_review');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_all_reviews');
        $this->reviewHelper()->openReview($searchData);
        //Verification
        $this->reviewHelper()->verifyReviewData($reviewData);
    }

    public function frontendReviewSpecialCharactersDataProvider()
    {
        return array(
            array('review_long_values'),
            array('review_special_symbols'),
        );
    }
}