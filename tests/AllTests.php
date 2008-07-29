<?php
class AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('All tests');
        $suite->addTest(Mage_AllTests::suite());
        return $suite;
    }
}
