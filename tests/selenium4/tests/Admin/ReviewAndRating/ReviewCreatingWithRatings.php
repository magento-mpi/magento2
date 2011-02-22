<?php

class Admin_ReviewAndRating_ReviewCreatingWithRatings extends TestCaseAbstract {

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
            'rating_for_stars' => 'Test-Rating-default_title',
            'rating_stars' => '3',
            //Star q-ty for 'rating_for_stars' Ratings. q-ty 'rating_stars' elements
            //must be equal to q-ty 'rating_for_stars' elements
            
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