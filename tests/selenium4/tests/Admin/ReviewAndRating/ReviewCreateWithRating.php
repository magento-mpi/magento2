<?php

class Admin_ReviewAndRating_ReviewCreateWithRating extends TestCaseAbstract {

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
    function testReviewCreateWithRating()
    {
        $reviewData = array(
                        'status'                    => 'Pending',
                        'product_rating'            => array('Test Rating(Default Value)' => 5),
                        'store_view_name_visible'   => array('SmokeTestStoreView','Default Store View'),
                        'nickname'                  => 'Test user',
                        'summary_of_review'         => 'Test review with rating',
                        'review_text'               => 'Test review text',
                        'search_product_sku'        => 'SP-01',
                        'search_product_name'       => 'Simple Product 01.Required Fields',
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateReview($reviewData);
        }
    }

}