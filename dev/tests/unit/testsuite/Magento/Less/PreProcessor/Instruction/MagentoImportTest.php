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
     * @var \Magento\View\DesignInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $design;

    /**
     * @var \Magento\View\File\CollectorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileSource;

    /**
     * @var \Magento\Less\PreProcessor\ErrorHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $errorHandler;

    /**
     * @var \Magento\View\Asset\PreProcessor\ModuleNotation\Resolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $notationResolver;

    /**
     * @var \Magento\Less\PreProcessor\Instruction\Import
     */
    private $object;

    protected function setUp()
    {
        $this->design = $this->getMockForAbstractClass('\Magento\View\DesignInterface');
        $this->fileSource = $this->getMockForAbstractClass('\Magento\View\File\CollectorInterface');
        $this->errorHandler = $this->getMockForAbstractClass('\Magento\Less\PreProcessor\ErrorHandlerInterface');
        $this->notationResolver = $this->getMock('\Magento\View\Asset\PreProcessor\ModuleNotation\Resolver');

        $this->object = new \Magento\Less\PreProcessor\Instruction\MagentoImport(
            $this->design, $this->fileSource, $this->errorHandler, $this->notationResolver
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
        $asset = $this->getMock('\Magento\View\Asset\FileId', array(), array(), '', false);
        $this->notationResolver->expects($this->once())
            ->method('convertModuleNotationToPath')
            ->with($asset, $foundPath)
            ->will($this->returnValue($resolvedPath));
        $theme = $this->getMockForAbstractClass('\Magento\View\Design\ThemeInterface');
        $this->design->expects($this->once())
            ->method('getDesignTheme')
            ->will($this->returnValue($theme));
        $files = [];
        foreach ($foundFiles as $file) {
            $fileObject = $this->getMock('Magento\View\File', array(), array(), '', false);
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
        $actual = $this->object->process($originalContent, 'css', $asset);
        $this->assertSame([$expectedContent, 'css'], $actual);
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
                "@import 'some/file.css';\n@import 'theme/some/file.css';\n",
            ],
            'modular' => [
                '//@magento_import "Magento_Module::some/file.css";',
                'Magento_Module::some/file.css',
                'Magento_Module/some/file.css',
                [
                    ['module' => 'Magento_Module', 'filename' => 'some/file.css'],
                ],
                "@import 'Magento_Module::some/file.css';\n",
            ],
        ];
    }

    public function testProcessNoImport()
    {
        $originalContent = 'color: #000000;';
        $expectedContent = 'color: #000000;';

        $asset = $this->getMock('\Magento\View\Asset\FileId', array(), array(), '', false);
        $this->notationResolver->expects($this->never())
            ->method('convertModuleNotationToPath');
        $actual = $this->object->process($originalContent, 'css', $asset);
        $this->assertSame([$expectedContent, 'css'], $actual);
    }

    public function testProcessException()
    {
        $asset = $this->getMock('\Magento\View\Asset\FileId', array(), array(), '', false);
        $exception = new \LogicException('Error happened');
        $this->notationResolver->expects($this->once())
            ->method('convertModuleNotationToPath')
            ->with($asset, 'some/file.css')
            ->will($this->throwException($exception));
        $this->errorHandler->expects($this->once())
            ->method('processException')
            ->with($exception);
        $actual = $this->object->process('//@magento_import "some/file.css";', 'css', $asset);
        $this->assertSame(['', 'css'], $actual);
    }
}
