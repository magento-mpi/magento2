<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\PreProcessor\Instruction;

class ImportTest extends \PHPUnit_Framework_TestCase
{
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
        $this->notationResolver = $this->getMock('\Magento\View\Asset\PreProcessor\ModuleNotation\Resolver');
        $this->object = new \Magento\Less\PreProcessor\Instruction\Import($this->notationResolver);
    }

    /**
     * @param string $originalContent
     * @param string $foundPath
     * @param string $resolvedPath
     * @param string $expectedContent
     *
     * @dataProvider processDataProvider
     */
    public function testProcess($originalContent, $foundPath, $resolvedPath, $expectedContent)
    {
        $asset = $this->getMock('\Magento\View\Asset\FileId', array(), array(), '', false);
        $this->notationResolver->expects($this->once())
            ->method('convertModuleNotationToPath')
            ->with($asset, $foundPath)
            ->will($this->returnValue($resolvedPath));
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
                '@import (type) "some/file.css" media;',
                'some/file.css',
                'some/file.css',
                "@import (type) 'some/file.css' media;",
            ],
            'modular, with extension' => [
                '@import (type) "Magento_Module::something.css" media;',
                'Magento_Module::something.css',
                'Magento_Module/something.css',
                "@import (type) 'Magento_Module/something.css' media;",
            ],
            'modular, no extension' => [
                '@import (type) "Magento_Module::something" media;',
                'Magento_Module::something.less',
                'Magento_Module/something.less',
                "@import (type) 'Magento_Module/something.less' media;",
            ],
            'no type' => [
                '@import "Magento_Module::something.css" media;',
                'Magento_Module::something.css',
                'Magento_Module/something.css',
                "@import 'Magento_Module/something.css' media;",
            ],
            'no media' => [
                '@import (type) "Magento_Module::something.css";',
                'Magento_Module::something.css',
                'Magento_Module/something.css',
                "@import (type) 'Magento_Module/something.css';",
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

    /**
     * @covers resetRelatedFiles
     */
    public function testGetRelatedFiles()
    {
        $this->assertSame([], $this->object->getRelatedFiles());

        $asset = $this->getMock('\Magento\View\Asset\FileId', array(), array(), '', false);
        $this->notationResolver->expects($this->once())
            ->method('convertModuleNotationToPath')
            ->with($asset, 'Magento_Module::something.css')
            ->will($this->returnValue('Magento_Module/something.css'));
        $this->object->process('@import (type) "Magento_Module::something.css" media;', 'css', $asset);
        $this->object->process('color: #000000;', 'css', $asset);

        $expected = [['Magento_Module::something.css', $asset]];
        $this->assertSame($expected, $this->object->getRelatedFiles());

        $this->object->resetRelatedFiles();
        $this->assertSame([], $this->object->getRelatedFiles());
    }
}
