<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backup
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backup_SnapshotTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $methods
     * @return Mage_Backup_Snapshot
     */
    public function testGetDbBackupFilename()
    {
        $manager = $this->getMock(
            'Mage_Backup_Snapshot',
            array('getBackupFilename')
        );

        $file = 'var/backup/2.gz';
        $manager->expects($this->once())
            ->method('getBackupFilename')
            ->will($this->returnValue($file));

        $model = new Mage_Backup_Snapshot();
        $model->setDbBackupManager($manager);
        $this->assertEquals($file, $model->getDbBackupFilename());
    }
}
