<?php

class Admin_ReviewAndRating_ReviewVerification extends TestCaseAbstract {

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
            'search_product_sku' => 'Xandr simple product 1',
            'search_product_name' => 'Xandr simple product 1',
            'status' => 'Pending',
            'search_review_nickname' => 'Test user',
            'search_review_title' => 'Test review',
            //'search_review_id' => '',
            //'search_review_detail'=> '',
            'search_review_product_name'=> 'Xandr simple product 1',
            'search_review_product_sku'=> 'Xandr simple product 1',

            'store_view_name' => 'Xandr shop',
            'nickname' => 'Test user',
            'review_text' => 'Test review text',
        );
        if ($this->model->doLogin()) {
            $this->model->ReviewVerification($review_rateData);
        }
    }

}