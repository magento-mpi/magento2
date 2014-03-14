<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class PublisherTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\View\Publisher */
    protected $publisher;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\App\Filesystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $filesystemMock;

    /** @var \Magento\View\FileSystem|\PHPUnit_Framework_MockObject_MockObject */
    protected $viewFileSystem;

    /** @var \Magento\View\Asset\PreProcessor\PreProcessorInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $preProcessorMock;

    /** @var \Magento\View\Publisher\FileFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $fileFactoryMock;

    /** @var \Magento\View\Publisher\FileInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $publisherFileMock;

    /** @var \Magento\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $rootDirectory;

    /** @var \Magento\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $tmpDirectory;

    /** @var \Magento\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $pubDirectory;

    protected function setUp()
    {
        $this->markTestIncomplete('MAGETWO-21654');
        $this->rootDirectory = $this->getMock('Magento\Filesystem\Directory\WriteInterface', [], [], '', false);
        $this->tmpDirectory = $this->getMock('Magento\Filesystem\Directory\WriteInterface', [], [], '', false);
        $this->pubDirectory = $this->getMock('Magento\Filesystem\Directory\WriteInterface', [], [], '', false);

        $this->filesystemMock = $this->getMock('Magento\App\Filesystem', [], [], '', false);
        $this->filesystemMock->expects($this->any())
            ->method('getDirectoryWrite')
            ->will($this->returnCallback(array($this, 'getDirectoryWriteCallback')));

        $this->viewFileSystem = $this->getMock('Magento\View\FileSystem', [], [], '', false);
        $this->preProcessorMock = $this->getMock('Magento\View\Asset\PreProcessor\PreProcessorInterface');
        $this->fileFactoryMock = $this->getMock('Magento\View\Publisher\FileFactory', [], [], '', false);
        $this->publisherFileMock = $this->getMock('Magento\View\Publisher\FileInterface', [], [], '', false);
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->publisher = $this->objectManagerHelper->getObject(
            'Magento\View\Publisher',
            [
                'filesystem' => $this->filesystemMock,
                'viewFileSystem' => $this->viewFileSystem,
                'preProcessor' => $this->preProcessorMock,
                'fileFactory' => $this->fileFactoryMock
            ]
        );
    }

    /**
     * @param string $param
     * @return \Magento\Filesystem\Directory\WriteInterface|\PHPUnit_Framework_MockObject_MockObject
     * @throws \UnexpectedValueException
     */
    public function getDirectoryWriteCallback($param)
    {
        switch ($param) {
            case \Magento\App\Filesystem::ROOT_DIR:
                return $this->rootDirectory;
            case \Magento\App\Filesystem::VAR_DIR:
                return $this->tmpDirectory;
            case \Magento\App\Filesystem::STATIC_VIEW_DIR:
                return $this->pubDirectory;
            default:
                throw new \UnexpectedValueException('Directory write callback received wrong value: ' . $param);
        }
    }

    /**
     * @param string $filePath
     * @param array $params
     */
    protected function prepareCommonMocks($filePath, $params)
    {
        $this->fileFactoryMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo($filePath), $this->equalTo($params))
            ->will($this->returnValue($this->publisherFileMock));

        $this->preProcessorMock->expects($this->once())
            ->method('process')
            ->with($this->equalTo($this->publisherFileMock), $this->equalTo($this->tmpDirectory))
            ->will($this->returnValue($this->publisherFileMock));
    }

    public function testGetViewFile()
    {
        $filePath = '/some/file.js';
        $params = array('param1' => 'param 1', 'param2' => 'param 2');
        $expectedResult = 'result';

        $this->publisherFileMock->expects($this->once())
            ->method('getSourcePath')
            ->will($this->returnValue($expectedResult));
        $this->publisherFileMock->expects($this->once())
            ->method('isSourceFileExists')
            ->will($this->returnValue(true));
        $this->prepareCommonMocks($filePath, $params);

        $actualResult = $this->publisher->getViewFile($filePath, $params);
        $this->assertSame($expectedResult, $actualResult);
    }
}
