<?php

class Mage_Tax_AllTests
{
    public static function suite()
    {
        $suite = new Mage_PHPUnit_TestSuite('Mage/Tax/AllTests');
        $suite->addTest(Mage_Tax_Model_AllTests::suite());
        return $suite;
    }
}
