<?php
class Mage_Tag_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Mage/Tag/AllTests');
        $suite->addTest(Mage_Tag_Controllers_AllTests::suite());
        $suite->addTest(Mage_Tag_Model_AllTests::suite());
        return $suite;
    }
}
