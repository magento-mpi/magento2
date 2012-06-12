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
class Mage_Selenium_AutoloaderTest extends Mage_PHPUnit_TestCase
{
    /**
     * @covers Mage_Selenium_Autoloader::autoload
     */
    public function testAutoload()
    {
        $this->assertFalse(Mage_Selenium_Autoloader::autoload('not-existing-class'));

        $className = 'Mage_Selenium_Autoloader';
        $this->assertTrue(Mage_Selenium_Autoloader::autoload($className));
    }
}