<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View;

class DeployedFilesManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $area
     * @param string $themePath
     * @param string $file
     * @param string $module
     * @param string $expected
     * @dataProvider buildDeployedFilePathDataProvider
     */
    public function testBuildDeployedFilePath($area, $themePath, $file, $module, $expected)
    {
        $actual = \Magento\Framework\View\DeployedFilesManager::buildDeployedFilePath(
            $area,
            $themePath,
            $file,
            $module,
            $expected
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public static function buildDeployedFilePathDataProvider()
    {
        return array(
            'no module' => array('a', 't', 'f', null, 'a/t/f'),
            'with module' => array('a', 't', 'f', 'm', 'a/t/m/f')
        );
    }
}
