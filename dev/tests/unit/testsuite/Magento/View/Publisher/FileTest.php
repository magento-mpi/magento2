<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Publisher;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class FileTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\View\Publisher\File */
    protected $file;

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
     * @var string
     */
    protected $libDir = '/some/lib/web/dir';

    /**
     * @var string
     */
    protected $viewStaticDir = '/some/view/static/dir';

    /**
     * @var string
     */
    protected $themeDir = '/some/theme/dir';

    /**
     * @var \Magento\View\Path|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $path;

    /**
     * @param string $filePath
     * @param array $viewParams
     * @param null|string $sourcePath
     */
    protected function getModelMock($filePath, $viewParams, $sourcePath = null)
    {
        $this->rootDirectory = $this->getMock('Magento\Filesystem\Directory\WriteInterface');

        $this->filesystemMock = $this->getMock('Magento\App\Filesystem', [], [], '', false);
        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with($this->equalTo(\Magento\App\Filesystem::ROOT_DIR))
            ->will($this->returnValue($this->rootDirectory));
        $this->filesystemMock->expects($this->any())
            ->method('getPath')
            ->with($this->anything())
            ->will($this->returnCallback(array($this, 'getPathCallback')));
        $this->serviceMock = $this->getMock('Magento\View\Service', [], [], '', false);
        $this->readerMock = $this->getMock('Magento\Module\Dir\Reader', [], [], '', false);
        $this->viewFileSystem = $this->getMock('Magento\View\FileSystem', [], [], '', false);

        if ($sourcePath) {
            $this->rootDirectory->expects($this->any())
                ->method('getRelativePath')
                ->with($sourcePath)
                ->will($this->returnValue('related\\' . $sourcePath));
            $this->rootDirectory->expects($this->any())
                ->method('isExist')
                ->with('related\\' . $sourcePath)
                ->will($this->returnValue(true));
        }

        $this->path = $this->getMock('\Magento\View\Path');
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->file = $this->objectManagerHelper->getObject(
            'Magento\View\Publisher\File',
            [
                'filesystem' => $this->filesystemMock,
                'viewService' => $this->serviceMock,
                'modulesReader' => $this->readerMock,
                'viewFileSystem' => $this->viewFileSystem,
                'path' => $this->path,
                'filePath' => $filePath,
                'viewParams' => $viewParams,
                'sourcePath' => $sourcePath
            ]
        );
    }

    /**
     * @param string $param
     * @return string
     * @throws \UnexpectedValueException
     */
    public function getPathCallback($param)
    {
        switch ($param) {
            case \Magento\App\Filesystem::LIB_WEB:
                return $this->libDir;
            case \Magento\App\Filesystem::STATIC_VIEW_DIR:
                return $this->viewStaticDir;
            case \Magento\App\Filesystem::THEMES_DIR:
                return $this->themeDir;
            default:
                throw new \UnexpectedValueException('Path callback received wrong value: ' . $param);
        }
    }

    /**
     * @param null|string $sourcePath
     * @param bool $expected
     * @internal param null|string $sourceFile
     * @dataProvider isPublicationAllowedDataProvider
     */
    public function testIsPublicationAllowed($sourcePath, $expected)
    {
        $filePath = 'some/file/path';
        $this->getModelMock($filePath, ['some', 'array'], $sourcePath);

        $this->assertSame($expected, $this->file->isPublicationAllowed());
    }

    /**
     * @return array
     */
    public function isPublicationAllowedDataProvider()
    {
        return [
            [null, true],
            ['some/interesting/path/to/file', true],
            ['some\interesting\path\to\file', true],
            [$this->libDir . '/path/to/file', true],
            [$this->libDir . '\path\to\file', true],
            [$this->viewStaticDir . '\path\to\file', false],
            [$this->viewStaticDir . '/path/to/file', false],
            [$this->themeDir . '/path/to/file', true],
            [$this->themeDir . '\path\to\file', true],
        ];
    }

    /**
     * @param string $filePath
     * @param array $viewParams
     * @param string|null $sourcePath
     * @param string $expectedSubPath
     * @param string $expected
     * @dataProvider buildUniquePathDataProvider
     */
    public function testBuildUniquePath($filePath, $viewParams, $sourcePath, $expectedSubPath, $expected)
    {
        $this->getModelMock($filePath, $viewParams, $sourcePath);
        $this->path->expects($this->once())
            ->method('getRelativePath')
            ->with($viewParams['area'], $viewParams['themeModel'], $viewParams['locale'], $viewParams['module'])
            ->will($this->returnValue($expectedSubPath));
        $this->assertSame($expected, $this->file->buildUniquePath());
    }

    /**
     * @return array
     */
    public function buildUniquePathDataProvider()
    {
        $themModelWithPath = $this->getMock('Magento\View\Design\ThemeInterface', [], [], '', false);
        $themModelWithPath->expects($this->any())->method('getThemePath')->will($this->returnValue('theme/path'));
        $themModelWithId = $this->getMock('Magento\View\Design\ThemeInterface', [], [], '', false);
        $themModelWithId->expects($this->any())->method('getId')->will($this->returnValue(11));
        return [
            'theme with path' => [
                'filePath' => 'some/file/path',
                'viewParams' => [
                    'themeModel' => $themModelWithPath,
                    'area' => 'frontend',
                    'locale' => 'en_US',
                    'module' => 'some_module',
                ],
                'sourcePath' => null,
                'expectedSubPath' => 'frontend/theme/path/en_US/some_module',
                'expected' => 'frontend/theme/path/en_US/some_module/some/file/path'
            ],
            'theme with id' => [
                'filePath' => 'some/file/path2',
                'viewParams' => [
                    'themeModel' => $themModelWithId,
                    'area' => 'backend',
                    'locale' => 'en_EN',
                    'module' => 'some_other_module',
                ],
                'sourcePath' => null,
                'expectedSubPath' => 'backend/_theme11/en_EN/some_other_module',
                'expected' => 'backend/_theme11/en_EN/some_other_module/some/file/path2'
            ],
            'theme without any data' => [
                'filePath' => 'some/file/path3',
                'viewParams' => [
                    'themeModel' => $this->getMock('Magento\View\Design\ThemeInterface', [], [], '', false),
                    'locale' => 'fr_FR',
                    'area' => 'some_area',
                    'module' => null,
                ],
                'sourcePath' => null,
                'expectedSubPath' => 'some_area/_view/fr_FR',
                'expected' => 'some_area/_view/fr_FR/some/file/path3'
            ],
        ];
    }
}
