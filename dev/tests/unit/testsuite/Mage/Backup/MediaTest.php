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

class Mage_Backup_MediaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $methods
     * @return Mage_Backup_Snapshot
     */
    protected function _getSnapshotMock(array $methods = array())
    {
        $snapshot = $this->getMock(
            'Mage_Backup_Snapshot',
            $methods + array('create', 'rollback', 'getDbBackupFilename')
        );
        $snapshot->expects($this->any())
            ->method('create')
            ->will($this->returnValue(true));
        $snapshot->expects($this->any())
            ->method('rollback')
            ->will($this->returnValue(true));
        $snapshot->expects($this->once())
            ->method('getDbBackupFilename')
            ->will($this->returnValue('var/backup/2.gz'));

        return $snapshot;
    }

    public function testCreate()
    {
        $snapshot = $this->_getSnapshotMock();

        $model = new Mage_Backup_Media();
        $model->setSnapshotManager($snapshot);

        $rootDir = __DIR__ . DIRECTORY_SEPARATOR . '_files';
        $model->setRootDir($rootDir);

        $this->assertTrue($model->create());

        $this->assertEquals(
            array(
                $rootDir . DIRECTORY_SEPARATOR . 'code',
                $rootDir . DIRECTORY_SEPARATOR .'var' . DIRECTORY_SEPARATOR . 'tmp',
            ),
            $model->getSnapshotManager()->getIgnorePaths()
        );
    }

    public function testRollback()
    {
        $snapshot = $this->_getSnapshotMock();

        $model = new Mage_Backup_Media();
        $model->setSnapshotManager($snapshot);

        $rootDir = __DIR__ . DIRECTORY_SEPARATOR . '_files';
        $model->setRootDir($rootDir);

        $this->assertTrue($model->rollback());

        $this->assertEquals(
            array(
                $rootDir . DIRECTORY_SEPARATOR . 'code',
                $rootDir . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'tmp',
            ),
            $model->getSnapshotManager()->getIgnorePaths()
        );
    }

    public function getSetGetSnapshotManager()
    {
        $snapshot = new Mage_Backup_Snapshot();
        $model = new Mage_Backup_Media();
        $this->assertInstanceOf('Mage_Backup_Media', $model->setSnapshotManager($snapshot));
        $this->assertEquals($snapshot, $model->getSnapshotManager());
    }

    /**
     * @param string $method
     * @param $parameter
     * @dataProvider methodsProvider
     */
    public function testProxyMethod($method, $parameter)
    {
        $snapshot = $this->getMock('Mage_Backup_Snapshot', array($method));
        $snapshot->expects($this->once())
            ->method($method)
            ->with($parameter)
            ->will($this->returnValue($snapshot));

        $model = new Mage_Backup_Media();
        $model->setSnapshotManager($snapshot);
        $this->assertEquals($model, $model->$method($parameter));
    }

    public static function methodsProvider()
    {
        return array(
            array('setBackupExtension', 'test'),
            array('setResourceModel', new Mage_Backup_Media()),
            array('setTime', 1),
            array('setBackupsDir', 'test/test'),
            array('addIgnorePaths', 'test/test'),
            array('setRootDir', 'test/test'),
        );
    }
}
