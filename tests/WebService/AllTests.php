<?php
class WebService_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('WebService/AllTests');
        $suite->addTest(WebService_Customer_AllTests::suite());
        return $suite;
    }
}