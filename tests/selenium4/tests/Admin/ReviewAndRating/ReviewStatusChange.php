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
        $review_rateData = array(
            'status' => 'Approved',
            'search_review_nickname' => 'Test user',
            'search_review_title' => 'Test review',
            //'search_review_id' => '',
            //'search_review_detail'=> '',
            'search_review_product_name' => 'Xandr simple product 1',
            'search_review_product_sku' => 'Xandr simple product 1',
        );
        if ($this->model->doLogin()) {
            $this->model->changeReviewStatus($review_rateData);
        }
    }

}