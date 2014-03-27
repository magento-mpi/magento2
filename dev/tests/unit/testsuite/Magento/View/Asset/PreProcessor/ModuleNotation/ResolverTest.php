<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\PreProcessor\ModuleNotation;

class ResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\FileId|\PHPUnit_Framework_MockObject_MockObject
     */
    private $asset;
    
    /**
     * @var \Magento\View\Asset\PreProcessor\ModuleNotation\Resolver;
     */
    private $object;

    protected function setUp()
    {
        $this->asset = $this->getMock('Magento\View\Asset\FileId', array(), array(), '', false);
        $this->object = new \Magento\View\Asset\PreProcessor\ModuleNotation\Resolver();
    }

    public function testConvertModuleNotationToPathNoModularSeparator()
    {
        $this->asset->expects($this->never())->method('getRelativePath');
        $this->asset->expects($this->never())->method('createSimilar');
        $textNoSeparator = 'name_without_double_colon.ext';
        $this->assertEquals(
            $textNoSeparator,
            $this->object->convertModuleNotationToPath($this->asset, $textNoSeparator)
        );
    }

    /**
     * @param string $assetRelPath
     * @param string $relatedFieldId
     * @param string $similarRelPath
     * @param string $expectedResult
     * @dataProvider convertModuleNotationToPathModularSeparatorDataProvider
     */
    public function testConvertModuleNotationToPathModularSeparator(
        $assetRelPath, $relatedFieldId, $similarRelPath, $expectedResult
    ) {
        $similarasset = $this->getMock('Magento\View\Asset\FileId', array(), array(), '', false);
        $similarasset->expects($this->any())
            ->method('getRelativePath')
            ->will($this->returnValue($similarRelPath));
        $this->asset->expects($this->once())
            ->method('getRelativePath')
            ->will($this->returnValue($assetRelPath));
        $this->asset->expects($this->once())
            ->method('createRelative')
            ->with($relatedFieldId)
            ->will($this->returnValue($similarasset));
        $this->assertEquals(
            $expectedResult,
            $this->object->convertModuleNotationToPath($this->asset, $relatedFieldId)
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
