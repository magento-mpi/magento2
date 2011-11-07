<?php
/**
 * Test case for Mage_Customer_Model_Customer
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Api Team <api-team@magento.com>
 */
class Mage_Customer_Model_AllTests
{
    /**
     * Get suite with all tests
     *
     * @static
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Magento API Customer Models');
        $suite->addTestSuite('Mage_Customer_Model_Example');
        return $suite;
    }
}

