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

namespace Magento\Backup;

class MediaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Dir
     */
    protected $_dirMock;

    /**
     * @var \Magento\BackupFactory
     */
    protected $_backupFactoryMock;

    protected function setUp()
    {
        $this->_dirMock = $this->getMock('Magento\Core\Model\Dir', array(), array(), '', false);
        $this->_backupFactoryMock = $this->getMock('Magento\BackupFactory', array(), array(), '', false);
    }
    /**
     * @param string $action
     * @dataProvider actionProvider
     */
    public function testAction($action)
    {
        $snapshot = $this->getMock(
            'Magento\Backup\Snapshot',
            array('create', 'rollback', 'getDbBackupFilename'),
            array($this->_dirMock, $this->_backupFactoryMock)
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

        $rootDir = __DIR__ . DIRECTORY_SEPARATOR . '_files';

        $model = new \Magento\Backup\Media($snapshot);
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
            array($this->_dirMock, $this->_backupFactoryMock));
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
