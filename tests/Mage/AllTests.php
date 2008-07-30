<?php
class Mage_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Mage/AllTests');
        $suite->addTest(Mage_Tag_AllTests::suite());
        // $suite->addTest(Mage_..._AllTests::suite());
        return $suite;
    }
}
