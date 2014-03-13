<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\PreProcessor;

class ModuleNotationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\PreProcessor\ModuleNotation
     */
    protected $moduleNotation;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $cssResolverMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $assetMock;

    protected function setUp()
    {
        $this->assetMock = $this->getMock('Magento\View\Asset\FileId', array(), array(), '', false);
        $this->cssResolverMock = $this->getMock('Magento\View\Url\CssResolver', array(), array(), '', false);
        $this->moduleNotation = new \Magento\View\Asset\PreProcessor\ModuleNotation($this->cssResolverMock);
    }

    public function testProcess()
    {
        $content = 'ol.favicon {background: url(Magento_Theme::favicon.ico)}';
        $replacedContent = 'Foo_Bar/images/logo.gif';
        $contentType = 'type';
        $this->cssResolverMock->expects($this->once())
            ->method('replaceRelativeUrls')
            ->with($content, $this->isInstanceOf('Closure'))
            ->will($this->returnValue($replacedContent));

        $this->assertSame(
            array($replacedContent, $contentType),
            $this->moduleNotation->process($content, $contentType, $this->assetMock)
        );
    }

    public function testConvertModuleNotationToPathNoModularSeparator()
    {
        $this->assetMock->expects($this->never())->method('getRelativePath');
        $this->assetMock->expects($this->never())->method('createSimilar');
        $textNoSeparator = 'name_without_double_colon.ext';
        $this->assertEquals(
            $textNoSeparator,
            ModuleNotation::convertModuleNotationToPath($this->assetMock, $textNoSeparator)
        );
    }

    /**
     * @param $assetRelPath
     * @param $relatedFieldId
     * @param $similarRelPath
     * @param $expectedResult
     * @dataProvider convertModuleNotationToPathModularSeparatorDataProvider
     */
    public function testConvertModuleNotationToPathModularSeparator(
        $assetRelPath, $relatedFieldId, $similarRelPath, $expectedResult
    ) {
        $similarAssetMock = $this->getMock('Magento\View\Asset\FileId', array(), array(), '', false);
        $similarAssetMock->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnValue($similarRelPath));
        $this->assetMock->expects($this->once())
            ->method('getRelativePath')
            ->will($this->returnValue($assetRelPath));
        $this->assetMock->expects($this->once())
            ->method('createSimilar')
            ->with($relatedFieldId)
            ->will($this->returnValue($similarAssetMock));
        $this->assertEquals(
            $expectedResult,
            ModuleNotation::convertModuleNotationToPath($this->assetMock, $relatedFieldId)
        );
    }

    /**
     * @return array
     */
    public function convertModuleNotationToPathModularSeparatorDataProvider()
    {
        return array(
            'same module' => array(
                'area/theme/locale/Foo_Bar/styles/style.css',
                'Foo_Bar::images/logo.gif',
                'area/theme/locale/Foo_Bar/images/logo.gif',
                '../images/logo.gif'
            ),
            'non-modular refers to modular' => array(
                'area/theme/locale/css/admin.css',
                'Bar_Baz::images/logo.gif',
                'area/theme/locale/Bar_Baz/images/logo.gif',
                '../Bar_Baz/images/logo.gif'
            ),
            'different modules' => array(
                'area/theme/locale/Foo_Bar/styles/style.css',
                'Bar_Baz::images/logo.gif',
                'area/theme/locale/Bar_Baz/images/logo.gif',
                '../../Bar_Baz/images/logo.gif'
            )
        );
    }
}
