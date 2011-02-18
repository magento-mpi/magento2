<?php

class Admin_ReviewAndRating_ReviewCreatingNeg extends TestCaseAbstract {

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
            'rating_for_stars' => array('Test-Rating-default_title', '123'),
            'rating_stars' => array('3', '5'),
            'store_view_name_visible' => array('SmokeTestStoreView', 'Default Store View'),
            'nickname' => '',
            'summary_of_review' => '',
            'review_text' => '',
            'search_product_sku' => 'Xandr simple product 1',
            'search_product_name' => 'Xandr simple product 1',
        );
        if ($this->model->doLogin()) {
            $this->model->createReview($review_rateData);
        }
    }

}