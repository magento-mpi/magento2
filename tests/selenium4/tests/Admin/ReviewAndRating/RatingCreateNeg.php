<?php

class Admin_ReviewAndRating_RatingCreateNeg extends TestCaseAbstract {

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
    function testRatingCreateNeg()
    {
        $reviewData = array(
            'title_for_stores' => array('Default Value' => 'Price'),
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateRating($reviewData);
        }
    }

}