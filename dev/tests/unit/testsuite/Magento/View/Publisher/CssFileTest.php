<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Publisher;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class CssFileTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\View\Publisher\CssFile */
    protected $cssFile;

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
     * @param string $filePath
     * @param array $viewParams
     * @param null|string $sourcePath
     * @param bool $developerMode
     */
    protected function getModelMock(
        $filePath,
        $viewParams,
        $sourcePath = null,
        $developerMode = false
    ) {
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
        if ($developerMode) {
            $this->serviceMock->expects($this->once())
                ->method('getAppMode')
                ->will($this->returnValue('developer'));
        }

        $this->readerMock = $this->getMock('Magento\Module\Dir\Reader', [], [], '', false);
        $this->viewFileSystem = $this->getMock('Magento\View\FileSystem', [], [], '', false);
        $this->viewFileSystem->expects($this->any())
            ->method('getAppMode')
            ->will($this->returnValue(\Magento\App\State::MODE_DEVELOPER));

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

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->cssFile = $this->objectManagerHelper->getObject(
            'Magento\View\Publisher\CssFile',
            [
                'filesystem' => $this->filesystemMock,
                'viewService' => $this->serviceMock,
                'modulesReader' => $this->readerMock,
                'viewFileSystem' => $this->viewFileSystem,
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
     * @param bool $developerMode
     * @internal param null|string $sourceFile
     * @dataProvider isPublicationAllowedDataProvider
     */
    public function testIsPublicationAllowed($sourcePath, $expected, $developerMode)
    {
        $filePath = 'some/css/path';
        $this->getModelMock($filePath, ['some', 'array'], $sourcePath, $developerMode);
        $this->assertSame($expected, $this->cssFile->isPublicationAllowed());
    }

    /**
     * @return array
     */
    public function isPublicationAllowedDataProvider()
    {
        return [
            [null, true, false],
            ['some/interesting/path/to/file', true, false],
            ['some\interesting\path\to\file', true, false],
            [$this->libDir . '/path/to/file', true, false],
            [$this->libDir . '\path\to\file', true, false],
            [$this->viewStaticDir . '\path\to\file', false, false],
            [$this->viewStaticDir . '/path/to/file', false, false],
            [$this->themeDir . '/path/to/file', true, false],
            [$this->themeDir . '\path\to\file', true, false],
            [$this->libDir . '/path/to/file', true, false],
            [$this->libDir . '\path\to\file', true, false],
            [$this->viewStaticDir . '\path\to\file', true, true],
            [$this->viewStaticDir . '/path/to/file', true, true],
        ];
    }
}
