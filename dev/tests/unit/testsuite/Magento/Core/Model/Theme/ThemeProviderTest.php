<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Theme;

class ThemeProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetByFullPath()
    {
        $path = 'frontend/Magento/luma';
        $collectionFactory = $this->getMock(
            'Magento\Core\Model\Resource\Theme\CollectionFactory',
            array('create'),
            array(),
            '',
            false
        );
        $collectionMock = $this->getMock('Magento\Core\Model\Resource\Theme\Collection', array(), array(), '', false);
        $theme = $this->getMock('Magento\Framework\View\Design\ThemeInterface', array(), array(), '', false);
        $collectionMock->expects(
            $this->once()
        )->method(
            'getThemeByFullPath'
        )->with(
            $path
        )->will(
            $this->returnValue($theme)
        );
        $collectionFactory->expects($this->once())->method('create')->will($this->returnValue($collectionMock));
        $themeFactory = $this->getMock('\Magento\Core\Model\ThemeFactory', array(), array(), '', false);

        $themeProvider = new ThemeProvider($collectionFactory, $themeFactory);
        $this->assertSame($theme, $themeProvider->getThemeByFullPath($path));
    }

    public function testGetById()
    {
        $themeId = 755;
        $collectionFactory = $this->getMock(
            'Magento\Core\Model\Resource\Theme\CollectionFactory',
            array(),
            array(),
            '',
            false
        );
        $theme = $this->getMock('Magento\Core\Model\Theme', array(), array(), '', false);
        $theme->expects($this->once())->method('load')->with($themeId)->will($this->returnSelf());
        $themeFactory = $this->getMock('\Magento\Core\Model\ThemeFactory', array('create'), array(), '', false);
        $themeFactory->expects($this->once())->method('create')->will($this->returnValue($theme));

        $themeProvider = new ThemeProvider($collectionFactory, $themeFactory);
        $this->assertSame($theme, $themeProvider->getThemeById($themeId));
    }
}
