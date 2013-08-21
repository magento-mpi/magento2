<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backup
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backup_MediaTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $methods
     * @return Magento_Backup_Snapshot
     */
    protected function _getSnapshotMock(array $methods = array())
    {
        $snapshot = $this->getMock(
            'Magento_Backup_Snapshot',
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

    /**
     * @param string $action
     * @dataProvider actionProvider
     */
    public function testAction($action)
    {
        $snapshot = $this->_getSnapshotMock();

        $model = new Magento_Backup_Media($snapshot);

        $rootDir = __DIR__ . DIRECTORY_SEPARATOR . '_files';
        $model->setRootDir($rootDir);

        $this->assertTrue($model->$action());

        $this->assertEquals(
            array(
                $rootDir . DIRECTORY_SEPARATOR . 'code',
                $rootDir . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'tmp',
            ),
            $snapshot->getIgnorePaths()
        );
    }

    /**
     * @expectedException Magento_Exception
     */
    public function testConstruct()
    {
        new Magento_Backup_Media(new StdClass);
    }

    public static function actionProvider()
    {
        return array(
            array('create'),
            array('rollback'),
        );
    }

    /**
     * @param string $method
     * @param $parameter
     * @dataProvider methodsProvider
     */
    public function testProxyMethod($method, $parameter)
    {
        $snapshot = $this->getMock('Magento_Backup_Snapshot', array($method));
        $snapshot->expects($this->once())
            ->method($method)
            ->with($parameter)
            ->will($this->returnValue($snapshot));

        $model = new Magento_Backup_Media($snapshot);
        $this->assertEquals($model, $model->$method($parameter));
    }

    public static function methodsProvider()
    {
        return array(
            array('setBackupExtension', 'test'),
            array('setResourceModel', new Magento_Backup_Media()),
            array('setTime', 1),
            array('setBackupsDir', 'test/test'),
            array('addIgnorePaths', 'test/test'),
            array('setRootDir', 'test/test'),
        );
    }
}
