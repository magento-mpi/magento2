<?php

class Mage_Tax_Model_AllTests
{
    public static function suite()
    {
        $suite = new Mage_PHPUnit_TestSuite('Mage/Tax/Model/AllTests');
        $suite->addTestSuite('Mage_Tax_Model_CalculationTest');
        return $suite;
    }
}
