<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Backup;

class SnapshotTest extends \PHPUnit_Framework_TestCase
{
    public function testGetDbBackupFilename()
    {
        $filesystem = $this->getMock('Magento\Framework\Filesystem', array(), array(), '', false);
        $backupFactory = $this->getMock('Magento\Framework\Backup\Factory', array(), array(), '', false);
        $manager = $this->getMock(
            'Magento\Framework\Backup\Snapshot',
            array('getBackupFilename'),
            array($filesystem, $backupFactory)
        );

        $file = 'var/backup/2.gz';
        $manager->expects($this->once())->method('getBackupFilename')->will($this->returnValue($file));

        $model = new \Magento\Framework\Backup\Snapshot($filesystem, $backupFactory);
        $model->setDbBackupManager($manager);
        $this->assertEquals($file, $model->getDbBackupFilename());
    }
}
