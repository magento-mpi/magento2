<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\ModuleNotation;

class ResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\View\Asset\File|\PHPUnit_Framework_MockObject_MockObject
     */
    private $asset;

    /**
     * @var \Magento\View\Asset\Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $assetRepo;
    
    /**
     * @var \Magento\View\Asset\ModuleNotation\Resolver;
     */
    private $object;

    protected function setUp()
    {
        $this->asset = $this->getMock('Magento\View\Asset\File', array(), array(), '', false);
        $this->assetRepo = $this->getMock('Magento\View\Asset\Repository', array(), array(), '', false);
        $this->object = new \Magento\View\Asset\ModuleNotation\Resolver($this->assetRepo);
    }

    public function testConvertModuleNotationToPathNoModularSeparator()
    {
        $this->asset->expects($this->never())->method('getPath');
        $this->assetRepo->expects($this->never())->method('createUsingContext');
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
        $similarAsset = $this->getMock('Magento\View\Asset\File', array(), array(), '', false);
        $similarAsset->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($similarRelPath));
        $this->asset->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue($assetRelPath));
        $this->assetRepo->expects($this->once())
            ->method('createSimilar')
            ->with($relatedFieldId, $this->asset)
            ->will($this->returnValue($similarAsset));
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
