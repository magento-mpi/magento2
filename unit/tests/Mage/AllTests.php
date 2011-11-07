<?php
/**
 * Test case for Mage
 *
 * @category   Mage
 * @package    Mage
 * @author     Magento Api Team <api-team@magento.com>
 */
class Mage_AllTests
{
    /**
     * Get suite with all tests
     *
     * @static
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Magento API Customer Tests');
        $suite->addTest(Mage_Customer_Model_AllTests::suite());
        return $suite;
    }
}
