<?php
class Mage_Tag_Model_AllTests
{
    public static function suite()
    {
        // $suite = new PHPUnit_Framework_TestSuite('Mage/Tag/Model tests');
        $suite = new Mage_FrontendSuite('Mage/Tag/Model tests');
        $suite->addTestSuite('Mage_Tag_Model_TagTest');
        return $suite;
    }
}
