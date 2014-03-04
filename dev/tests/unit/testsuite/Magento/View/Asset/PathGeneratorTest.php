<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset;

class PathGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PathGenerator
     */
    private $object;

    protected function setUp()
    {
        $this->object = new PathGenerator;
    }

    public function testGetPathUsingTheme()
    {
        $theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');
        $theme->expects($this->once())
            ->method('getThemePath')
            ->will($this->returnValue('theme_path'));
        $actual = $this->object->getPathUsingTheme('area', $theme, 'locale', 'module');
        $expected = 'area/theme_path/locale/module';
        $this->assertSame($expected, $actual);
    }

    /**
     * @param string $themeId
     * @param string $expected
     *
     * @dataProvider gtPathUsingThemeNoPathDataProvider
     */
    public function testGetPathUsingThemeNoPath($themeId, $expected)
    {
        $theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');
        $theme->expects($this->once())
            ->method('getThemePath')
            ->will($this->returnValue(null));
        $theme->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($themeId));
        $actual = $this->object->getPathUsingTheme('area', $theme, 'locale', 'module');
        $this->assertSame($expected, $actual);
    }

    public function gtPathUsingThemeNoPathDataProvider()
    {
        return array(
            'with id' => array('_theme_id', 'area/_theme_theme_id/locale/module'),
            'no id'   => array('', 'area/_view/locale/module'),
        );
    }

    /**
     * @param string $area
     * @param string $theme
     * @param string $locale
     * @param string $module
     * @param string $expected
     * @dataProvider getPathDataProvider
     */
    public function testGetPath($area, $theme, $locale, $module, $expected)
    {
        $this->assertEquals($expected, $this->object->getPath($area, $theme, $locale, $module));
    }

    /**
     * @return array
     */
    public function getPathDataProvider()
    {
        return array(
            'with module'  => array('2', '3', '4', '5', '2/3/4/5'),
            'empty module' => array('2', '3', '4', '', '2/3/4'),
            'no module'    => array('2', '3', '4', null, '2/3/4'),
        );
    }
}
