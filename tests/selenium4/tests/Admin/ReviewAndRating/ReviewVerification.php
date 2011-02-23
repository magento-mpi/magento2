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
            'search_product_sku'            => 'SP-01',
            'search_product_name'           => 'Simple Product 01.Required Fields',
            'search_review_product_sku'     => 'SP-01',
            'search_review_product_name'    => 'Simple Product 01.Required Fields',
            'search_review_title'           => 'Test review',
            'search_review_nickname'        => 'Test user',
            'search_review_detail'          => 'Test review text',
        );
        if ($this->model->doLogin()) {
            $this->model->ReviewVerification($review_rateData);
        }
    }

}