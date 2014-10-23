<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backup\Model\Fs;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $filesystem = $this->getMockBuilder('\Magento\Framework\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();
        $directoryWrite = $this->getMockBuilder(
            '\Magento\Framework\Filesystem\Directory\WriteInterface'
        )->disableOriginalConstructor()->getMock();
        $filesystem->expects($this->any())->method('getDirectoryWrite')->will($this->returnValue($directoryWrite));

        $backupData = $this->getMockBuilder('\Magento\Backup\Helper\Data')->disableOriginalConstructor()->getMock();
        $backupData->expects($this->any())->method('getExtensions')->will($this->returnValue(array()));

        $directoryWrite->expects($this->any())->method('create')->with('backups');
        $directoryWrite->expects($this->any())->method('getAbsolutePath')->with('backups');

        $helper->getObject(
            'Magento\Backup\Model\Fs\Collection',
            array('filesystem' => $filesystem, 'backupData' => $backupData)
        );
    }
}
