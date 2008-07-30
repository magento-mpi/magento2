<?php
class Mage_Tag_Controllers_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Mage/Tag/Controllers/AllTests');
        $suite->addTestSuite('Mage_Tag_Controllers_ProductTest');
        $suite->addTestSuite('Mage_Tag_Controllers_IndexTest');
        return $suite;
    }
}
