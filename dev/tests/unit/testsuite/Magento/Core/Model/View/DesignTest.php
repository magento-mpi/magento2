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
            '\Magento\View\Design\Theme\FlyweightFactory', array(), array(), '', false
        );
        $config = $this->getMockForAbstractClass('\Magento\App\ConfigInterface');
        $storeConfig = $this->getMockForAbstractClass('\Magento\Core\Model\Store\ConfigInterface');
        $themeFactory = $this->getMock('\Magento\Core\Model\ThemeFactory');
        $this->app = $this->getMock('\Magento\Core\Model\App', array(), array(), '', false);
        $state = $this->getMock('\Magento\App\State', array(), array(), '', false);
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
}
