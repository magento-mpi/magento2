<?php
class WebService_Customer_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('WebService/Customer/AllTests');
        $suite->addTestSuite('WebService_Customer_GroupTest');
        return $suite;
    }
}