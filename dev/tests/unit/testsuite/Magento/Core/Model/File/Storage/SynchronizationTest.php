<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\File\Storage;

use Magento\Framework\App\Filesystem\DirectoryList;

class SynchronizationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test fir synchronize method
     */
    public function testSynchronize()
    {
        $content = 'content';
        $relativeFileName = 'config.xml';
        $filePath = realpath(__DIR__ . '/_files/');

        $storageFactoryMock = $this->getMock(
            'Magento\Core\Model\File\Storage\DatabaseFactory',
            array('create', '_wakeup'),
            array(),
            '',
            false
        );
        $storageMock = $this->getMock(
            'Magento\Core\Model\File\Storage\Database',
            array('getContent', 'getId', 'loadByFilename', '__wakeup'),
            array(),
            '',
            false
        );
        $storageFactoryMock->expects($this->once())->method('create')->will($this->returnValue($storageMock));

        $storageMock->expects($this->once())->method('getContent')->will($this->returnValue($content));
        $storageMock->expects($this->once())->method('getId')->will($this->returnValue(true));
        $storageMock->expects($this->once())->method('loadByFilename');

        $file = $this->getMock(
            'Magento\Framework\Filesystem\File\Write',
            array('lock', 'write', 'unlock', 'close'),
            array(),
            '',
            false
        );
        $file->expects($this->once())->method('lock');
        $file->expects($this->once())->method('write')->with($content);
        $file->expects($this->once())->method('unlock');
        $file->expects($this->once())->method('close');
        $directory = $this->getMock(
            'Magento\Framework\Filesystem\Direcoty\Write',
            array('openFile', 'getRelativePath'),
            array(),
            '',
            false
        );
        $directory->expects($this->once())->method('getRelativePath')->will($this->returnArgument(0));
        $directory->expects($this->once())->method('openFile')->with($filePath)->will($this->returnValue($file));
        $filesystem = $this->getMock(
            'Magento\Framework\App\Filesystem',
            array('getDirectoryWrite'),
            array(),
            '',
            false
        );
        $filesystem->expects(
            $this->once()
        )->method(
            'getDirectoryWrite'
        )->with(
                DirectoryList::PUB_DIR
        )->will(
            $this->returnValue($directory)
        );

        $model = new Synchronization($storageFactoryMock, $filesystem);
        $model->synchronize($relativeFileName, $filePath);
    }
}
