<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Publisher;

class FileAbstractTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\View\Publisher\FileAbstract|\PHPUnit_Framework_MockObject_MockObject */
    protected $fileAbstract;

    /** @var \Magento\Framework\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $filesystemMock;

    /** @var \Magento\Framework\View\Service|\PHPUnit_Framework_MockObject_MockObject */
    protected $serviceMock;

    /** @var \Magento\Module\Dir\Reader|\PHPUnit_Framework_MockObject_MockObject */
    protected $readerMock;

    /** @var \Magento\Framework\View\FileSystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $viewFileSystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rootDirectory;

    /**
     * @param string $filePath
     * @param array $viewParams
     * @param null|string $sourcePath
     * @param null|string $fallback
     */
    protected function initModelMock($filePath, $viewParams, $sourcePath = null, $fallback = null)
    {
        $this->rootDirectory = $this->getMock('Magento\Framework\Filesystem\Directory\WriteInterface');

        $this->filesystemMock = $this->getMock('Magento\Framework\App\Filesystem', array(), array(), '', false);
        $this->filesystemMock->expects(
            $this->once()
        )->method(
            'getDirectoryWrite'
        )->with(
            $this->equalTo(\Magento\Framework\App\Filesystem::ROOT_DIR)
        )->will(
            $this->returnValue($this->rootDirectory)
        );
        $this->serviceMock = $this->getMock('Magento\Framework\View\Service', array(), array(), '', false);
        $this->readerMock = $this->getMock('Magento\Module\Dir\Reader', array(), array(), '', false);
        $this->viewFileSystem = $this->getMock('Magento\Framework\View\FileSystem', array(), array(), '', false);
        if ($fallback) {
            $this->viewFileSystem->expects(
                $this->once()
            )->method(
                'getViewFile'
            )->with(
                $this->equalTo($filePath),
                $this->equalTo($viewParams)
            )->will(
                $this->returnValue('fallback\\' . $fallback)
            );

            $this->rootDirectory->expects(
                $this->once()
            )->method(
                'getRelativePath'
            )->with(
                'fallback\\' . $fallback
            )->will(
                $this->returnValue('related\\' . $fallback)
            );
        }

        $this->fileAbstract = $this->getMockForAbstractClass(
            'Magento\Framework\View\Publisher\FileAbstract',
            array(
                'filesystem' => $this->filesystemMock,
                'viewService' => $this->serviceMock,
                'modulesReader' => $this->readerMock,
                'viewFileSystem' => $this->viewFileSystem,
                'filePath' => $filePath,
                'allowDuplication' => true,
                'viewParams' => $viewParams,
                'sourcePath' => $sourcePath
            )
        );
    }

    /**
     * @param string $filePath
     * @param string $expected
     * @dataProvider getExtensionDataProvider
     */
    public function testGetExtension($filePath, $expected)
    {
        $this->initModelMock($filePath, array('some', 'array'));
        $this->assertSame($expected, $this->fileAbstract->getExtension());
    }

    /**
     * @return array
     */
    public function getExtensionDataProvider()
    {
        return array(array('some\path\file.css', 'css'), array('some\path\noextension', ''));
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
        $this->initModelMock($filePath, array('some', 'array'), $sourcePath, $fallback);
        if ($fallback) {
            $this->rootDirectory->expects(
                $this->once()
            )->method(
                'isExist'
            )->with(
                'related\\' . $fallback
            )->will(
                $this->returnValue($isExist)
            );
        }

        $this->assertSame($expected, $this->fileAbstract->isSourceFileExists());
    }

    /**
     * @return array
     */
    public function isSourceFileExistsDataProvider()
    {
        return array(
            array(
                'filePath' => 'some\file',
                'isExist' => false,
                'sourcePath' => null,
                'fallback' => null,
                'expectedResult' => false
            ),
            array(
                'filePath' => 'some\file2',
                'isExist' => false,
                'sourcePath' => 'some\sourcePath',
                'fallback' => null,
                'expectedResult' => false
            ),
            array(
                'filePath' => 'some\file2',
                'isExist' => false,
                'sourcePath' => null,
                'fallback' => 'some\fallback\file',
                'expectedResult' => false
            ),
            array(
                'filePath' => 'some\file2',
                'isExist' => true,
                'sourcePath' => null,
                'fallback' => 'some\fallback\file',
                'expectedResult' => true
            )
        );
    }

    public function testGetFilePath()
    {
        $filePath = 'test\me';
        $this->initModelMock($filePath, array('some', 'array'));
        $this->assertSame($filePath, $this->fileAbstract->getFilePath());
    }

    public function testGetViewParams()
    {
        $viewParams = array('some', 'array');
        $this->initModelMock('some\file', $viewParams);
        $this->assertSame($viewParams, $this->fileAbstract->getViewParams());
    }

    public function testBuildPublicViewFilename()
    {
        $this->initModelMock('some\file', array());
        $this->serviceMock->expects($this->once())->method('getPublicDir')->will($this->returnValue('/some/pub/dir'));

        $this->fileAbstract->expects(
            $this->once()
        )->method(
            'buildUniquePath'
        )->will(
            $this->returnValue('some/path/to/file')
        );
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
        $this->initModelMock($filePath, array('some', 'array'), $sourcePath, $fallback);
        if ($fallback) {
            $this->rootDirectory->expects(
                $this->once()
            )->method(
                'isExist'
            )->with(
                'related\\' . $fallback
            )->will(
                $this->returnValue($isExist)
            );
        }

        $this->assertSame($expected, $this->fileAbstract->getSourcePath());
    }

    /**
     * @return array
     */
    public function getSourcePathDataProvider()
    {
        return array(
            array(
                'filePath' => 'some\file',
                'isExist' => false,
                'sourcePath' => null,
                'fallback' => null,
                'expectedResult' => null
            ),
            array(
                'filePath' => 'some\file2',
                'isExist' => false,
                'sourcePath' => 'some\sourcePath',
                'fallback' => null,
                'expectedResult' => null
            ),
            array(
                'filePath' => 'some\file2',
                'isExist' => false,
                'sourcePath' => null,
                'fallback' => 'some\fallback\file',
                'expectedResult' => null
            ),
            array(
                'filePath' => 'some\file2',
                'isExist' => true,
                'sourcePath' => null,
                'fallback' => 'some\fallback\file',
                'expectedResult' => 'fallback\some\fallback\file'
            )
        );
    }

    /**
     * @dataProvider sleepDataProvider
     */
    public function test__sleep($expected)
    {
        $this->initModelMock('some\file', array());
        $this->assertEquals($expected, $this->fileAbstract->__sleep());
    }

    /**
     * @return array
     */
    public function sleepDataProvider()
    {
        return array(
            array(
                array(
                    'filePath',
                    'extension',
                    'viewParams',
                    'sourcePath',
                    'allowDuplication',
                    'isPublicationAllowed',
                    'isFallbackUsed',
                    'isSourcePathProvided'
                )
            )
        );
    }
}
