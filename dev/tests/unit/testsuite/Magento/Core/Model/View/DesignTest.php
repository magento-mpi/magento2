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
namespace Magento\Core\Model\View;

class DesignTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\App|\PHPUnit_Framework_MockObject_MockObject
     */
    private $app;

    /**
     * @var \Magento\Core\Model\View\Design::__construct
     */
    private $model;

    protected function setUp()
    {
        $storeManager = $this->getMockForAbstractClass('\Magento\Core\Model\StoreManagerInterface');
        $flyweightThemeFactory = $this->getMock(
            '\Magento\Framework\View\Design\Theme\FlyweightFactory', array(), array(), '', false
        );
        $config = $this->getMockForAbstractClass('\Magento\Framework\App\ConfigInterface');
        $storeConfig = $this->getMockForAbstractClass('\Magento\Core\Model\Store\ConfigInterface');
        $themeFactory = $this->getMock('\Magento\Core\Model\ThemeFactory');
        $this->app = $this->getMock('\Magento\Core\Model\App', array(), array(), '', false);
        $state = $this->getMock('\Magento\Framework\App\State', array(), array(), '', false);
        $themes = array();
        $this->model = new \Magento\Core\Model\View\Design(
            $storeManager, $flyweightThemeFactory, $config, $storeConfig, $themeFactory, $this->app, $state, $themes
        );
    }

    public function testGetLocale()
    {
        $expected = 'locale';
        $locale = $this->getMock('\Magento\Core\Model\LocaleInterface');
        $locale->expects($this->once())
            ->method('getLocaleCode')
            ->will($this->returnValue($expected));
        $this->app->expects($this->once())
            ->method('getLocale')
            ->will($this->returnValue($locale));
        $actual = $this->model->getLocale();
        $this->assertSame($expected, $actual);
    }

    /**
     * @param string $themePath
     * @param string $themeId
     * @param string $expectedResult
     * @dataProvider getThemePathDataProvider
     */
    public function testGetThemePath($themePath, $themeId, $expectedResult)
    {
        $theme = $this->getMockForAbstractClass('\Magento\Framework\View\Design\ThemeInterface');
        $theme->expects($this->once())->method('getThemePath')->will($this->returnValue($themePath));
        $theme->expects($this->any())->method('getId')->will($this->returnValue($themeId));
        $this->assertEquals($expectedResult, $this->model->getThemePath($theme));
    }

    /**
     * @return array
     */
    public function getThemePathDataProvider()
    {
        return array(
            array('some_path', '', 'some_path'),
            array('', '2', \Magento\Framework\View\DesignInterface::PUBLIC_THEME_DIR . '2'),
            array('', '', \Magento\Framework\View\DesignInterface::PUBLIC_VIEW_DIR),
        );
    }
}
