<?php
/**
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

use Magento\UrlInterface;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $fileId
     * @param array $params
     * @param string $isSecureExpected
     * @param string $expectedResult
     * @dataProvider getViewUrlDataProvider
     */
    public function testGetViewFileUrl($fileId, $params, $isSecureExpected, $expectedResult)
    {
        $service = $this->getMock('\Magento\View\service', array(), array(), '', false);
        $service->expects($this->once())
            ->method('updateDesignParams')
            ->will($this->returnCallback(array($this, 'updateDesignParams')))
        ;
        $baseUrl = $this->getMockForAbstractClass('\Magento\UrlInterface');
        $baseUrl->expects($this->once())
            ->method('getBaseUrl')
            ->with(array('_type' => UrlInterface::URL_TYPE_STATIC, '_secure' => $isSecureExpected))
            ->will($this->returnValue('http://example.com/'))
        ;
        $object = new Url($service, $baseUrl);
        $this->assertEquals($expectedResult, $object->getViewFileUrl($fileId, $params));
    }

    /**
     * A mock callback replacement for "update design params" of View Service model
     *
     * @param array $params
     */
    public function updateDesignParams(array &$params)
    {
        $theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');
        $theme->expects($this->once())->method('getThemePath')->will($this->returnValue('theme/path'));
        $params['area'] = 'frontend';
        $params['locale'] = 'en_US';
        $params['themeModel'] = $theme;
    }

    /**
     * @return array
     */
    public function getViewUrlDataProvider()
    {
        return array(
            array('file.ext', array(), false, 'http://example.com/frontend/theme/path/en_US/file.ext'),
            array('Module::file.ext', array(), false, 'http://example.com/frontend/theme/path/en_US/Module/file.ext'),
            array('file.ext', array('_secure' => true), true, 'http://example.com/frontend/theme/path/en_US/file.ext'),
        );
    }

    public function testGetPathUsingTheme()
    {
        $theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');
        $theme->expects($this->once())->method('getThemePath')->will($this->returnValue('one'));
        $this->assertEquals('area/one/en_US/file', Url::getPathUsingTheme('file', 'area', $theme, 'en_US', ''));

        $theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');
        $theme->expects($this->once())->method('getThemePath')->will($this->returnValue(''));
        $theme->expects($this->once())->method('getId')->will($this->returnValue(5));
        $this->assertEquals(
            'area/_theme5/en_US/Module/file',
            Url::getPathUsingTheme('file', 'area', $theme, 'en_US', 'Module')
        );

        $theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');
        $theme->expects($this->once())->method('getThemePath')->will($this->returnValue(''));
        $theme->expects($this->once())->method('getId')->will($this->returnValue(0));
        $this->assertEquals('area/_view/en_US/file', Url::getPathUsingTheme('file', 'area', $theme, 'en_US'));
    }

    /**
     * @param string $file
     * @param string $area
     * @param string $theme
     * @param string $locale
     * @param string $module
     * @param string $expected
     * @dataProvider getFullyQualifiedPathDataProvider
     */
    public function testGetFullyQualifiedPath($file, $area, $theme, $locale, $module, $expected)
    {
        $this->assertEquals($expected, Url::getFullyQualifiedPath($file, $area, $theme, $locale, $module));
    }

    /**
     * @return array
     */
    public function getFullyQualifiedPathDataProvider()
    {
        return array(
            array('1', '2', '3', '4', '5', '2/3/4/5/1'),
            array('1', '2', '3', '4', '', '2/3/4/1'),
            array('1', '2', '3', '4', null, '2/3/4/1'),
        );
    }
}
