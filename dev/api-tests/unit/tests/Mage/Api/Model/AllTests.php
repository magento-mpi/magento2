<?php
/**
 * Test case for Mage_Customer_Model_*
 *
 * @category   Mage
 * @package    Mage_Api
 * @author     Magento Api Team <api-team@magento.com>
 */
class Mage_Api_Model_AllTests
{
    /**
     * Get suite with all tests
     *
     * @static
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Magento API Mage_Api Models');
        $suite->addTestSuite('Mage_Api_Model_SessionTest');
        return $suite;
    }
}

