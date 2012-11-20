<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Clear test method properties, it isn't needed to unset properties manually in tearDown() anymore
 */
class Magento_Test_ClearProperties
{
    /**
     * Clear test method properties after each test suite
     *
     * @param  PHPUnit_Framework_TestSuite $suite
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        $tests = $suite->tests();

        foreach ($tests as $test) {
            $reflectionClass = new ReflectionClass($test);
            $properties = $reflectionClass->getProperties();
            foreach ($properties as $property) {
                $property->setAccessible(true);
                $value = $property->getValue($test);
                if (is_object($value) && method_exists($value, '__destruct')) {
                    $value->__destruct();
                }
                $property->setValue($test, null);
            }
        }
    }
}
