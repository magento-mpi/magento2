<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\PreProcessor\Instruction;

class MagentoImportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\View\DesignInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $design;

    /**
     * @var \Magento\Framework\View\File\CollectorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileSource;

    /**
     * @var \Magento\Less\PreProcessor\ErrorHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $errorHandler;

    /**
     * @var \Magento\Framework\View\Asset\File|\PHPUnit_Framework_MockObject_MockObject
     */
    private $asset;

    /**
     * @var \Magento\Framework\View\Asset\Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $assetRepo;

    /**
     * @var \Magento\Less\PreProcessor\Instruction\Import
     */
    private $object;

    protected function setUp()
    {
        $this->design = $this->getMockForAbstractClass('\Magento\Framework\View\DesignInterface');
        $this->fileSource = $this->getMockForAbstractClass('\Magento\Framework\View\File\CollectorInterface');
        $this->errorHandler = $this->getMockForAbstractClass('\Magento\Less\PreProcessor\ErrorHandlerInterface');
        $this->asset = $this->getMock('\Magento\Framework\View\Asset\File', array(), array(), '', false);
        $this->asset->expects($this->any())->method('getContentType')->will($this->returnValue('css'));
        $this->assetRepo = $this->getMock('\Magento\Framework\View\Asset\Repository', array(), array(), '', false);
        $this->object = new \Magento\Less\PreProcessor\Instruction\MagentoImport(
            $this->design,
            $this->fileSource,
            $this->errorHandler,
            $this->assetRepo
        );
    }

    /**
     * @param string $originalContent
     * @param string $foundPath
     * @param string $resolvedPath
     * @param array $foundFiles
     * @param string $expectedContent
     *
     * @dataProvider processDataProvider
     */
    public function testProcess($originalContent, $foundPath, $resolvedPath, $foundFiles, $expectedContent)
    {
        $chain = new \Magento\Framework\View\Asset\PreProcessor\Chain($this->asset, $originalContent, 'css');
        $relatedAsset = $this->getMock('\Magento\Framework\View\Asset\File', array(), array(), '', false);
        $relatedAsset->expects($this->once())
            ->method('getFilePath')
            ->will($this->returnValue($resolvedPath));
        $this->assetRepo->expects($this->once())
            ->method('createRelated')
            ->with($foundPath, $this->asset)
            ->will($this->returnValue($relatedAsset));
        $theme = $this->getMockForAbstractClass('\Magento\Framework\View\Design\ThemeInterface');
        $this->design->expects($this->once())
            ->method('getDesignTheme')
            ->will($this->returnValue($theme));
        $files = [];
        foreach ($foundFiles as $file) {
            $fileObject = $this->getMock('Magento\Framework\View\File', array(), array(), '', false);
            $fileObject->expects($this->any())
                ->method('getModule')
                ->will($this->returnValue($file['module']));
            $fileObject->expects($this->any())
                ->method('getFilename')
                ->will($this->returnValue($file['filename']));
            $files[] = $fileObject;
        }
        $this->fileSource->expects($this->once())
            ->method('getFiles')
            ->with($theme, $resolvedPath)
            ->will($this->returnValue($files));
        $this->object->process($chain);
        $this->assertEquals($expectedContent, $chain->getContent());
        $this->assertEquals('css', $chain->getContentType());
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            'non-modular notation' => [
                '//@magento_import "some/file.css";',
                'some/file.css',
                'some/file.css',
                [
                    ['module' => null, 'filename' => 'some/file.css'],
                    ['module' => null, 'filename' => 'theme/some/file.css'],
                ],
                "@import 'some/file.css';\n@import 'some/file.css';\n",
            ],
            'modular' => [
                '//@magento_import "Magento_Module::some/file.css";',
                'Magento_Module::some/file.css',
                'some/file.css',
                [
                    ['module' => 'Magento_Module', 'filename' => 'some/file.css'],
                    ['module' => 'Magento_Two', 'filename' => 'some/file.css'],
                ],
                "@import 'Magento_Module::some/file.css';\n@import 'Magento_Two::some/file.css';\n",
            ],
        ];
    }

    public function testProcessNoImport()
    {
        $originalContent = 'color: #000000;';
        $expectedContent = 'color: #000000;';
        $chain = new \Magento\Framework\View\Asset\PreProcessor\Chain($this->asset, $originalContent, 'css');
        $this->assetRepo->expects($this->never())
            ->method('createRelated');
        $this->object->process($chain);
        $this->assertEquals($expectedContent, $chain->getContent());
        $this->assertEquals('css', $chain->getContentType());
    }

    public function testProcessException()
    {
        $chain = new \Magento\Framework\View\Asset\PreProcessor\Chain(
            $this->asset, '//@magento_import "some/file.css";', 'css'
        );
        $exception = new \LogicException('Error happened');
        $this->assetRepo->expects($this->once())
            ->method('createRelated')
            ->will($this->throwException($exception));
        $this->errorHandler->expects($this->once())
            ->method('processException')
            ->with($exception);
        $this->object->process($chain);
        $this->assertEquals('', $chain->getContent());
        $this->assertEquals('css', $chain->getContentType());
    }
}
