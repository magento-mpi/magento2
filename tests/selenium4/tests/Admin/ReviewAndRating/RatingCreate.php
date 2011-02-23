<?php

class Admin_ReviewAndRating_RatingCreate extends TestCaseAbstract {

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
    function testRatingCreate()
    {
        $reviewData = array(
            'title_for_stores'          => array(
                                            'Default Value' => 'Test Rating(Default Value)',
                                            'SmokeTestStoreView' => 'Test Rating(SmokeTestStoreView)'
            ),
            'store_view_name_visible'   => array('SmokeTestStoreView', 'Default Store View'),
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateRating($reviewData);
        }
    }

}