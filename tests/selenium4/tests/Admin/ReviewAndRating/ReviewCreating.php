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
            'status' => 'Pending', //Core::getEnvConfig('backend/rating_and_review/review/status'),
            'rating_select' => 'Test Rating', //Core::getEnvConfig('backend/rating_and_review/rating_title_default'),
            'rating_stars' => '4', //Core::getEnvConfig('backend/rating_and_review/review/stars'),
            'store_view_name' => 'Xandr shop', //Core::getEnvConfig('backend/rating_and_review/store_view_name'),
            'nickname' => 'Test user', //Core::getEnvConfig('backend/rating_and_review/review/nickname'),
            'summary_of_review' => 'Test review', //Core::getEnvConfig('backend/rating_and_review/review/summary_of_review'),
            'review_text' => 'Test review text', //Core::getEnvConfig('backend/rating_and_review/review/review_text'),
            'search_product_sku' => 'Xandr simple product 1',
            'search_product_name' => 'Xandr simple product 1',
        );
        if ($this->model->doLogin()) {
            $this->model->createReview($review_rateData);
        }
    }

}