<?php

class Admin_ReviewAndRating_RatingDelete extends TestCaseAbstract {

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
     * Test Rating Delete
     */
    function testRating()
    {
        $review_rateData = array(
            'search_rating_name' => 'Test-Rating-default_title',
            //'search_rating_id' => '',
        );
        if ($this->model->doLogin()) {
            $this->model->doDeleteRating($review_rateData);
        }
    }

}