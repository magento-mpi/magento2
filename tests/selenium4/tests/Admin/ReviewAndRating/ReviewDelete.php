<?php

class Admin_ReviewAndRating_ReviewDelete extends TestCaseAbstract {

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
     * Test Rating deleting
     */
    function testRating()
    {
        $review_rateData = array(
            'search_review_title' => 'Test review',//Core::getEnvConfig('backend/rating_and_review/review/summary_of_review'),
            'search_review_nickname' => 'Test user',//Core::getEnvConfig('backend/rating_and_review/review/nickname'),
        );
        if ($this->model->doLogin()) {
            $this->model->doDeleteReview($review_rateData);
        }
    }

}