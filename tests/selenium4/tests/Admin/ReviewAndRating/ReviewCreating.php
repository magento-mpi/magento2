<?php

class Admin_ReviewAndRating_ReviewCreating extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp()
    {
        $this->model = $this->getModel('admin/reviewandrating');
        $this->setUiNamespace();
    }

    /**
     * Test Review creating
     */
    function testRating()
    {
        $review_rateData = array(
            'status' => 'Pending',
            'title_for_store' => array('Test-Rating-title_for_SmokeTestStoreView', 'Test-Rating-title_for_DefaultStoreView'),
            'rating_stars' => array('Test-Rating-default_title_1', '123_4'),
            'store_view_name_title' =>array('SmokeTestStoreView','Default Store View'),
            'store_view_name_visible' => array('SmokeTestStoreView','Default Store View'),
            'nickname' => 'Test user', 
            'summary_of_review' => 'Test review', 
            'review_text' => 'Test review text', 
            'search_product_sku' => 'Xandr simple product 1',
            'search_product_name' => 'Xandr simple product 1',
        );
        if ($this->model->doLogin()) {
            $this->model->createReview($review_rateData);
        }
    }

}