<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Publisher;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class FileAbstractTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\View\Publisher\FileAbstract */
    protected $fileAbstract;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $filesystemMock;

    /** @var \Magento\View\Service|\PHPUnit_Framework_MockObject_MockObject */
    protected $serviceMock;

    /** @var \Magento\Module\Dir\Reader|\PHPUnit_Framework_MockObject_MockObject */
    protected $readerMock;

    /** @var \Magento\View\FileSystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $viewFileSystem;

    /**
     * @var \Magento\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rootDirectory;

    /**
     * @param string $filePath
     * @param bool $allowDuplication
     * @param array $viewParams
     * @param null|string $sourcePath
     * @param null|string $fallback
     */
    protected function getModelMock($filePath, $allowDuplication, $viewParams, $sourcePath = null, $fallback = null)
    {
        $this->rootDirectory = $this->getMock('Magento\Filesystem\Directory\WriteInterface');

        $this->filesystemMock = $this->getMock('Magento\App\Filesystem', [], [], '', false);
        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with($this->equalTo(\Magento\App\Filesystem::ROOT_DIR))
            ->will($this->returnValue($this->rootDirectory));
        $this->serviceMock = $this->getMock('Magento\View\Service', [], [], '', false);
        $this->readerMock = $this->getMock('Magento\Module\Dir\Reader', [], [], '', false);
        $this->viewFileSystem = $this->getMock('Magento\View\FileSystem', [], [], '', false);
        if ($fallback) {
            $this->viewFileSystem->expects($this->once())
                ->method('getViewFile')
                ->with($this->equalTo($filePath), $this->equalTo($viewParams))
                ->will($this->returnValue('fallback\\' . $fallback));

            $this->rootDirectory->expects($this->once())
                ->method('getRelativePath')
                ->with('fallback\\' . $fallback)
                ->will($this->returnValue('related\\' . $fallback));
        }


        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->fileAbstract = $this->getMockForAbstractClass(
            'Magento\View\Publisher\FileAbstract',
            [
                'filesystem' => $this->filesystemMock,
                'viewService' => $this->serviceMock,
                'modulesReader' => $this->readerMock,
                'viewFileSystem' => $this->viewFileSystem,
                'filePath' => $filePath,
                'allowDuplication' => $allowDuplication,
                'viewParams' => $viewParams,
                'sourcePath' => $sourcePath
            ]
        );
    }

    /**
     * @param string $filePath
     * @param string $expected
     * @dataProvider getExtensionDataProvider
     */
    public function testGetExtension($filePath, $expected)
    {
        $this->getModelMock($filePath, true, ['some', 'array']);
        $this->assertSame($expected, $this->fileAbstract->getExtension());
    }

    /**
     * @return array
     */
    public function getExtensionDataProvider()
    {
        return [
            ['some\path\file.css', 'css'],
            ['some\path\noextension', '']
        ];
    }

    /**
     * @param string $filePath
     * @param bool $isExist
     * @param null|string $sourcePath
     * @param string|null $fallback
     * @param bool $expected
     * @internal param null|string $sourceFile
     * @dataProvider isSourceFileExistsDataProvider
     */
    public function testIsSourceFileExists($filePath, $isExist, $sourcePath, $fallback, $expected)
    {
        $this->getModelMock($filePath, true, ['some', 'array'], $sourcePath, $fallback);
        if ($fallback) {
            $this->rootDirectory->expects($this->once())
                ->method('isExist')
                ->with('related\\' . $fallback)
                ->will($this->returnValue($isExist));
        }

        $this->assertSame($expected, $this->fileAbstract->isSourceFileExists());
    }

    /**
     * @return array
     */
    public function isSourceFileExistsDataProvider()
    {
        return [
            [
                'filePath' => 'some\file',
                'isExist' => false,
                'sourcePath' => null,
                'fallback' => null,
                'expectedResult' => false
            ],
            [
                'filePath' => 'some\file2',
                'isExist' => false,
                'sourcePath' => 'some\sourcePath',
                'fallback' => null,
                'expectedResult' => false
            ],
            [
                'filePath' => 'some\file2',
                'isExist' => false,
                'sourcePath' => null,
                'fallback' => 'some\fallback\file',
                'expectedResult' => false
            ],
            [
                'filePath' => 'some\file2',
                'isExist' => true,
                'sourcePath' => null,
                'fallback' => 'some\fallback\file',
                'expectedResult' => true
            ],
        ];
    }

    public function testGetFilePath()
    {
        $filePath = 'test\me';
        $this->getModelMock($filePath, true, ['some', 'array']);
        $this->assertSame($filePath, $this->fileAbstract->getFilePath());
    }

    public function testGetViewParams()
    {
        $viewParams = ['some', 'array'];
        $this->getModelMock('some\file', true, $viewParams);
        $this->assertSame($viewParams, $this->fileAbstract->getViewParams());
    }

    public function testBuildPublicViewFilename()
    {
        $this->getModelMock('some\file', true, []);
        $this->serviceMock->expects($this->once())
            ->method('getPublicDir')->will($this->returnValue('/some/pub/dir'));

        $this->fileAbstract->expects($this->once())
            ->method('buildUniquePath')
            ->will($this->returnValue('some/path/to/file'));
        $this->assertSame('/some/pub/dir/some/path/to/file', $this->fileAbstract->buildPublicViewFilename());
    }

    /**
     * @param string $filePath
     * @param bool $isExist
     * @param null|string $sourcePath
     * @param string|null $fallback
     * @param bool $expected
     * @internal param null|string $sourceFile
     * @dataProvider getSourcePathDataProvider
     */
    public function testGetSourcePath($filePath, $isExist, $sourcePath, $fallback, $expected)
    {
        $this->getModelMock($filePath, true, ['some', 'array'], $sourcePath, $fallback);
        if ($fallback) {
            $this->rootDirectory->expects($this->once())
                ->method('isExist')
                ->with('related\\' . $fallback)
                ->will($this->returnValue($isExist));
        }

        $this->assertSame($expected, $this->fileAbstract->getSourcePath());
    }

    /**
     * @return array
     */
    public function getSourcePathDataProvider()
    {
        return [
            [
                'filePath' => 'some\file',
                'isExist' => false,
                'sourcePath' => null,
                'fallback' => null,
                'expectedResult' => null
            ],
            [
                'filePath' => 'some\file2',
                'isExist' => false,
                'sourcePath' => 'some\sourcePath',
                'fallback' => null,
                'expectedResult' => null
            ],
            [
                'filePath' => 'some\file2',
                'isExist' => false,
                'sourcePath' => null,
                'fallback' => 'some\fallback\file',
                'expectedResult' => null
            ],
            [
                'filePath' => 'some\file2',
                'isExist' => true,
                'sourcePath' => null,
                'fallback' => 'some\fallback\file',
                'expectedResult' => 'fallback\some\fallback\file'
            ],
        ];
    }
}
