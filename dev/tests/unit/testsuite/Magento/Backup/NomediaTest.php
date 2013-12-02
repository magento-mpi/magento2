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
require_once __DIR__ . '/_files/Gz.php';
require_once __DIR__ . '/_files/Tar.php';
require_once __DIR__ . '/_files/Fs.php';
require_once __DIR__ . '/_files/Helper.php';
require_once(__DIR__ . '/_files/io.php');

class NomediaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Dir
     */
    protected $_dirMock;

    /**
     * @var \Magento\Backup\Factory
     */
    protected $_backupFactoryMock;

    /**
     * @var \Magento\Backup\Db
     */
    protected $_backupDbMock;

    protected function setUp()
    {
        $this->_backupDbMock = $this->getMock('Magento\Backup\Db', array(), array(), '', false);
        $this->_backupDbMock->expects($this->any())
            ->method('setBackupExtension')
            ->will($this->returnSelf());

        $this->_backupDbMock->expects($this->any())
            ->method('setTime')
            ->will($this->returnSelf());

        $this->_backupDbMock->expects($this->any())
            ->method('setBackupsDir')
            ->will($this->returnSelf());

        $this->_backupDbMock->expects($this->any())
            ->method('setResourceModel')
            ->will($this->returnSelf());

        $this->_backupDbMock->expects($this->any())
            ->method('getBackupPath')
            ->will($this->returnValue('\unexistingpath'));

        $this->_backupDbMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue(true));

        $this->_backupFactoryMock = $this->getMock('Magento\Backup\Factory', array(), array(), '', false);
        $this->_backupFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->_backupDbMock));
        $this->_dirMock = $this->getMock('Magento\App\Dir', array(), array(), '', false);
    }

    /**
     * @param string $action
     * @dataProvider actionProvider
     */
    public function testAction($action)
    {
        $this->_backupFactoryMock->expects($this->once())->method('create');

        $rootDir = __DIR__ . DS . '_files' . DS . 'data';

        $model = new \Magento\Backup\Nomedia($this->_dirMock, $this->_backupFactoryMock);
        $model->setRootDir($rootDir);
        $model->$action();
        $this->assertTrue($model->getIsSuccess());

        $this->assertEquals(
            array(
                $rootDir . DIRECTORY_SEPARATOR . 'media',
                $rootDir . DIRECTORY_SEPARATOR . 'pub' . DIRECTORY_SEPARATOR . 'media',
            ),
            $model->getIgnorePaths()
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
}
