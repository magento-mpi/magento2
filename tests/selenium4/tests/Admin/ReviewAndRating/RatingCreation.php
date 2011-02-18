<?php

class Admin_ReviewAndRating_RatingCreation extends TestCaseAbstract {

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
     * Test Rating creating
     */
    function testRating()
    {
        $review_rateData = array(
            'default_value' => 'Test Rating',//Core::getEnvConfig('backend/rating_and_review/rating_title_default'),
            'rating_title_store' => 'Test store rating',//Core::getEnvConfig('backend/rating_and_review/rating_title_store'),
            'store_view_name' => 'Xandr shop',//Core::getEnvConfig('backend/rating_and_review/store_view_name'),
            'store_view_value' => 'Xandr shop',//Core::getEnvConfig('backend/rating_and_review/store_view_value'),
        );
        if ($this->model->doLogin()) {
            $this->model->createRating($review_rateData);
        }
    }

}