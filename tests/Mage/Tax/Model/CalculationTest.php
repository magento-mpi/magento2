<?php

class Mage_Tax_Model_CalculationTest extends PHPUnit_Framework_TestCase
{
    protected $_calculator;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->_calculator = Mage::getModel('tax/calculation');
    }

    /**
     * @dataProvider calcTaxAmountDataProvider
     */
    public function testCalcTaxAmount($amount, $price, $taxRate, $inclTaxFlag, $roundFlag)
    {
        $this->assertEquals($amount, $this->_calculator->calcTaxAmount($price, $taxRate, $inclTaxFlag, $roundFlag));
    }

    public function calcTaxAmountDataProvider()
    {
        return array(
            array(0.21, 2.12,   10,     false,  true),
            array(1.45, 17.56,  8.25,   false,  true),
            array(51.57,676.62, 8.25,   true,   true),
            array(11.02,121.32, 9.99,   true,   true),
            array(1.52955, 18.54, 8.25,   false,false),
            //array(27.47916859122400, 360.5600, 8.25,   true,   false),
        );
    }
}