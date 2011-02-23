<?php

class Admin_ReviewAndRating_ReviewStatusChange extends TestCaseAbstract {

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
     * Test Rating approving
     */
    function testRating()
    {
        $reviewData = array(
            'search_review_product_sku'     => 'SP-01',
            'search_review_product_name'    => 'Simple Product 01.Required Fields',
            'search_review_title'           => 'Test review',
            'search_review_nickname'        => 'Test user',
            'search_review_detail'          => 'Test review text',
            'status'                        => 'Approved',
        );
        if ($this->model->doLogin()) {
            $this->model->changeReviewStatus($reviewData);
        }
    }

}