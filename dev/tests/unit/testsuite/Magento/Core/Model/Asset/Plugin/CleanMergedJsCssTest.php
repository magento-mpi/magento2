<?php
/**
 * Tests Magento\Core\Model\Asset\Plugin\CleanMergedJsCss
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Asset\Plugin;

class CleanMergedJsCssTest extends \Magento\Test\BaseTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Core\Helper\File\Storage\Database
     */
    private $databaseMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Framework\App\Filesystem
     */
    private $filesystemMock;

    /**
     * @var bool
     */
    private $hasBeenCalled = false;

    /**
     * @var \Magento\Core\Model\Asset\Plugin\CleanMergedJsCss
     */
    private $model;

    public function setUp()
    {
        parent::setUp();
        $this->filesystemMock = $this->basicMock('\Magento\Framework\App\Filesystem');
        $this->databaseMock = $this->basicMock('\Magento\Core\Helper\File\Storage\Database');
        $this->model = $this->objectManager->getObject('Magento\Core\Model\Asset\Plugin\CleanMergedJsCss',
            [
                'database' => $this->databaseMock,
                'filesystem' => $this->filesystemMock,
            ]
        );
    }

    public function testAroundCleanMergedJsCss()
    {
        $callable = function () {
            $this->hasBeenCalled = true;
        };
        $readDir = 'read directory';
        $mergedDir = $readDir .  '/' . \Magento\Framework\View\Asset\Merged::getRelativeDir();

        $readDirectoryMock = $this->basicMock('\Magento\Framework\Filesystem\Directory\ReadInterface');
        $readDirectoryMock->expects($this->any())->method('getAbsolutePath')->willReturn($readDir);

        $this->databaseMock->expects($this->once())
            ->method('deleteFolder')
            ->with($mergedDir);
        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryRead')
            ->with(\Magento\Framework\App\Filesystem::STATIC_VIEW_DIR)
            ->willReturn($readDirectoryMock);

        $this->model->aroundCleanMergedJsCss(
            $this->basicMock('\Magento\Framework\View\Asset\MergeService'),
            $callable
        );

        $this->assertTrue($this->hasBeenCalled);
    }
}
