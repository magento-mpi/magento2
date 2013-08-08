<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests usage of Magento_Core_Block_Abstract
 */
class Legacy_Magento_Core_Block_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests if methods are used with correct count of parameters
     *
     * @param string $file
     * @dataProvider phpFilesDataProvider
     */
    public function testGetChildHtml($file)
    {
        $result = Utility_Classes::getAllMatches(
            file_get_contents($file),
            "/(->getChildHtml\([^,()]+, ?[^,()]+,)/i"
        );
        $this->assertEmpty(
            $result,
            "3rd parameter is not needed anymore for getChildHtml() in '$file': " . print_r($result, true)
        );
        $result = Utility_Classes::getAllMatches(
            file_get_contents($file),
            "/(->getChildChildHtml\([^,()]+, ?[^,()]+, ?[^,()]+,)/i"
        );
        $this->assertEmpty(
            $result,
            "4th parameter is not needed anymore for getChildChildHtml() in '$file': " . print_r($result, true)
        );
    }

    public function phpFilesDataProvider()
    {
        return Utility_Files::init()->getPhpFiles();
    }
}
