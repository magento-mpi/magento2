<?php
/**
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

class DeployedFilesManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $pubDir
     * @param string $area
     * @param string $themePath
     * @param string $module
     * @param string $filePath
     * @param string $expectedSubPath
     * @param string $expected
     * @dataProvider getViewFileDataProvider
     */
    public function testGetPublicViewFile($pubDir, $area, $themePath, $module, $filePath, $expectedSubPath, $expected)
    {
        $this->_testGetFile(
            'getPublicViewFile', $pubDir, $area, $themePath, $module, $filePath, $expectedSubPath, $expected
        );
    }

    /**
     * @param string $pubDir
     * @param string $area
     * @param string $themePath
     * @param string $module
     * @param string $filePath
     * @param string $expectedSubPath
     * @param string $expected
     * @dataProvider getViewFileDataProvider
     */
    public function testGetViewFile($pubDir, $area, $themePath, $module, $filePath, $expectedSubPath, $expected)
    {
        $this->_testGetFile('getViewFile', $pubDir, $area, $themePath, $module, $filePath, $expectedSubPath, $expected);
    }

    /**
     * @return array
     */
    public static function getViewFileDataProvider()
    {
        return array(
            'no module' => array(
                '/dir', 'f', 'magento_demo', '', 'file.ext', 'f/magento_demo/', '/dir/f/magento_demo/file.ext'
            ),
            'with module' => array('/dir', 'b', 'theme', 'm', 'file.ext', 'b/theme//m', '/dir/b/theme/m/file.ext'),
        );
    }

    /**
     * @param string $method
     * @param string $pubDir
     * @param string $area
     * @param string $themePath
     * @param string $module
     * @param string $filePath
     * @param string $expectedSubPath
     * @param string $expected
     */
    protected function _testGetFile(
        $method, $pubDir, $area, $themePath, $module, $filePath, $expectedSubPath, $expected
    ) {
        $filesystem = $this->getMock('\Magento\App\FileSystem', array('getPath'), array(), '', false);
        $filesystem->expects($this->once())->method('getPath')->will($this->returnValue($pubDir));

        $theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');
        $theme->expects($this->at(0))->method('getThemePath')->will($this->returnValue(false));
        $theme->expects($this->at(1))->method('getThemePath')->will($this->returnValue(false));
        $theme->expects($this->at(2))->method('getThemePath')->will($this->returnValue($themePath));
        $theme->expects($this->at(3))->method('getThemePath')->will($this->returnValue($themePath));
        $theme->expects($this->any())->method('getParentTheme')->will($this->returnSelf());
        $params = array('themeModel' => $theme, 'area' => $area, 'module' => $module);

        $path = $this->getMock('Magento\View\Path');
        $path->expects($this->once())
            ->method('getFullyQualifiedPath')
            ->with($area, $themePath, '', $module)
            ->will($this->returnValue($expectedSubPath));

        $model = new DeployedFilesManager($filesystem, $path);
        $result = $model->$method($filePath, $params);
        $this->assertStringEndsWith($expected, $result);
    }
}
