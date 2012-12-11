<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Selenium_DriverTest extends Mage_PHPUnit_TestCase
{
    public function test__construct()
    {
        $instance = new Mage_Selenium_Driver();
        $this->assertInstanceOf('Mage_Selenium_Driver', $instance);
    }
}