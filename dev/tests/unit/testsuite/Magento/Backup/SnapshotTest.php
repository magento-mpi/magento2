<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Backup
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backup_SnapshotTest extends PHPUnit_Framework_TestCase
{
    public function testGetDbBackupFilename()
    {
        $dir = $this->getMock('Magento\Core\Model\Dir', array(), array(), '', false);
        $manager = $this->getMock('Magento\Backup\Snapshot', array('getBackupFilename'), array($dir));

        $file = 'var/backup/2.gz';
        $manager->expects($this->once())
            ->method('getBackupFilename')
            ->will($this->returnValue($file));

        $model = new \Magento\Backup\Snapshot($dir);
        $model->setDbBackupManager($manager);
        $this->assertEquals($file, $model->getDbBackupFilename());
    }
}
