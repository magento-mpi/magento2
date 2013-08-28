<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_View_DeployedFilesManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $area
     * @param string $themePath
     * @param string $locale
     * @param string $file
     * @param string $module
     * @param string $expected
     * @dataProvider buildDeployedFilePathDataProvider
     */
    public function testBuildDeployedFilePath($area, $themePath, $locale, $file, $module, $expected)
    {
        $actual = Magento_Core_Model_View_DeployedFilesManager::buildDeployedFilePath(
            $area, $themePath, $locale, $file, $module, $expected
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public static function buildDeployedFilePathDataProvider()
    {
        return array(
            'no module' => array('a', 't', 'l', 'f', null, str_replace('/', DIRECTORY_SEPARATOR, 'a/t/f')),
            'with module' => array('a', 't', 'l', 'f', 'm', str_replace('/', DIRECTORY_SEPARATOR, 'a/t/m/f')),
        );
    }
}
