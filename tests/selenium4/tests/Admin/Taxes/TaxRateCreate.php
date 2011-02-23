<?php

class Admin_Taxes_TaxRateCreate extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp()
    {
        $this->model = $this->getModel('admin/tax');
        $this->setUiNamespace();
    }

    /**
     * Test creating Tax Rate
     */
    function testCreateTaxRate()
    {
        $taxData = array(
                'tax_rate_identifier'   => 'Test Tax Rate',
                'country'               => 'United States',
                'state'                 => 'California',
                //'zip_post_is_range'     => 'No',
                //'zip_post_code'         => 90064,
                'zip_post_is_range'     => 'Yes',
                'zip_range_from'        => 90034,
                'zip_range_to'          => 90064,
                'tax_rate_percent'      => 10,
                'tax_titles'            => array(
                                        'Default Store View' => 'Title(Default Store View)',
                                        'SmokeTestStoreView' => 'Title(SmokeTestStoreView)'
            ),
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateTaxRate($taxData);
        }
    }

}