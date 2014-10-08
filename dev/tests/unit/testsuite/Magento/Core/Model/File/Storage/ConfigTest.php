<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\File\Storage;

use Magento\Framework\App\Filesystem\DirectoryList;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test for save method
     */
    public function testSave()
    {
        $config = array();
        $fileStorageMock = $this->getMock('Magento\Core\Model\File\Storage', array(), array(), '', false);
        $fileStorageMock->expects($this->once())->method('getScriptConfig')->will($this->returnValue($config));

        $file = $this->getMock(
            'Magento\Framework\Filesystem\File\Write',
            array('lock', 'write', 'unlock', 'close'),
            array(),
            '',
            false
        );
        $file->expects($this->once())->method('lock');
        $file->expects($this->once())->method('write')->with(json_encode($config));
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
        $directory->expects($this->once())->method('openFile')->with('cacheFile')->will($this->returnValue($file));
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
                DirectoryList::PUB
        )->will(
            $this->returnValue($directory)
        );
        $model = new \Magento\Core\Model\File\Storage\Config($fileStorageMock, $filesystem, 'cacheFile');
        $model->save();
    }
}
