<?php

class Admin_ReviewAndRating_ReviewCreatingNoRatings extends TestCaseAbstract {

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
            //There must be no ratings for selected stores for test case to work fine
            'store_view_name_visible' => array('SmokeTestStoreView', 'Default Store View'),
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