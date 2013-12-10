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

namespace Magento\Backup;

class MediaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Filesystem
     */
    protected $_filesystemMock;

    /**
     * @var \Magento\Backup\Factory
     */
    protected $_backupFactoryMock;

    protected function setUp()
    {
        $this->_filesystemMock = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $this->_backupFactoryMock = $this->getMock('Magento\Backup\Factory', array(), array(), '', false);
    }
    /**
     * @param string $action
     * @dataProvider actionProvider
     */
    public function testAction($action)
    {
        /** @var \Magento\Backup\Snapshot | \PHPUnit_Framework_MockObject_MockObject $snapshot */
        $snapshot = $this->getMock(
            'Magento\Backup\Snapshot',
            array('create', 'rollback', 'getDbBackupFilename'),
            array($this->_filesystemMock, $this->_backupFactoryMock)
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

        $rootDir = __DIR__ . '/_files';

        $model = new \Magento\Backup\Media($snapshot);
        $model->setRootDir($rootDir);

        $this->assertTrue($model->$action());

        $paths = $snapshot->getIgnorePaths();
        $path1 = str_replace('\\', '/', $paths[0]);
        $path2 = str_replace('\\', '/', $paths[1]);
        $rootDir = str_replace('\\', '/', $rootDir);

        $this->assertEquals($rootDir . '/code', $path1);
        $this->assertEquals($rootDir . '/var/tmp', $path2);
    }

    /**
     * @return array
     */
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
        $snapshot = $this->getMock('Magento\Backup\Snapshot',
            array($method),
            array($this->_filesystemMock, $this->_backupFactoryMock));
        $snapshot->expects($this->once())
            ->method($method)
            ->with($parameter)
            ->will($this->returnValue($snapshot));

        $model = new \Magento\Backup\Media($snapshot);
        $this->assertEquals($model, $model->$method($parameter));
    }

    /**
     * @return array
     */
    public function methodsProvider()
    {
        $snapshot = $this->getMock('Magento\Backup\Snapshot', array(), array(), '', false);
        return array(
            array('setBackupExtension', 'test'),
            array('setResourceModel', new \Magento\Backup\Media($snapshot)),
            array('setTime', 1),
            array('setBackupsDir', 'test/test'),
            array('addIgnorePaths', 'test/test'),
            array('setRootDir', 'test/test'),
        );
    }
}
