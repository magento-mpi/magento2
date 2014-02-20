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
     * @param string $expected
     * @dataProvider getPublicViewFileDataProvider
     */
    public function testGetPublicViewFile($pubDir, $area, $themePath, $module, $filePath, $expected)
    {
        $this->_testGetFile('getPublicViewFile', $pubDir, $area, $themePath, $module, $filePath, $expected);
    }

    /**
     * @return array
     */
    public static function getPublicViewFileDataProvider()
    {
        return array(
            'no module' => array('/dir', 'f', 'magento_demo', null, 'file.ext', '/dir/f/magento_demo/file.ext'),
            'with module' => array('/dir', 'b', 'theme', 'm', 'file.ext', '/dir/b/theme/m/file.ext'),
        );
    }

    /**
     * @param string $pubDir
     * @param string $area
     * @param string $themePath
     * @param string $module
     * @param string $filePath
     * @param string $expected
     * @dataProvider getPublicViewFileDataProvider
     */
    public function testGetViewFile($pubDir, $area, $themePath, $module, $filePath, $expected)
    {
        $this->_testGetFile('getViewFile', $pubDir, $area, $themePath, $module, $filePath, $expected);
    }

    /**
     * @param string $method
     * @param string $pubDir
     * @param string $area
     * @param string $themePath
     * @param string $module
     * @param string $filePath
     * @param string $expected
     */
    protected function _testGetFile($method, $pubDir, $area, $themePath, $module, $filePath, $expected)
    {
        $viewService = $this->getMock('\Magento\View\Service', array('getPublicDir'), array(), '', false);
        $viewService->expects($this->once())->method('getPublicDir')->will($this->returnValue($pubDir));

        $theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');
        $theme->expects($this->at(0))->method('getThemePath')->will($this->returnValue(false));
        $theme->expects($this->at(1))->method('getThemePath')->will($this->returnValue(false));
        $theme->expects($this->at(2))->method('getThemePath')->will($this->returnValue($themePath));
        $theme->expects($this->at(3))->method('getThemePath')->will($this->returnValue($themePath));
        $theme->expects($this->any())->method('getParentTheme')->will($this->returnSelf());
        $params = array('themeModel' => $theme, 'area' => $area, 'module' => $module);

        $model = new DeployedFilesManager($viewService);
        $result = $model->$method($filePath, $params);
        $this->assertStringEndsWith($expected, $result);
    }
}
