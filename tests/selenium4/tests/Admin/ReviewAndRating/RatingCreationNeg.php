<?php

class Admin_ReviewAndRating_RatingCreationNeg extends TestCaseAbstract {

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
            'default_title' => 'Price',
            'title_for_store' => array('Test-Rating-title_for_SmokeTestStoreView', 'Test-Rating-title_for_DefaultStoreView'),
            'store_view_name_title' =>array('SmokeTestStoreView','Default Store View'),
            'store_view_name_visible' => array('SmokeTestStoreView','Default Store View'),
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateRating($review_rateData);
        }
    }

}