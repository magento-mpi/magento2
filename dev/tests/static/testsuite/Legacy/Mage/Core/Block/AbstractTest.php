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
 * Tests usage of Mage_Core_Block_Abstract
 */
class Legacy_Mage_Core_Block_AbstractTest extends PHPUnit_Framework_TestCase
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
            "/(->(getChildHtml|getChildChildHtml)\([^,)]+, ?[^,)]+, ?[^,)]+\))/i"
        );
        $this->assertEmpty(
            $result,
            "3rd parameter is used while calling getChildHtml() in '$file': " . print_r($result, true)
        );
    }

    public function phpFilesDataProvider()
    {
        return Utility_Files::init()->getPhpFiles();
    }
}
