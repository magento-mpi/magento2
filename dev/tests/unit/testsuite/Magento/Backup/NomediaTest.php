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

class NomediaTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $dir = $this->getMock('Magento\Core\Model\Dir', array(), array(), '', false);
        $backupFactory = $this->getMock('Magento\BackupFactory', array(), array(), '', false);
        $snapshot = $this->getMock('Magento\Backup\Snapshot', array('create'), array($dir, $backupFactory));
        $snapshot->expects($this->any())
            ->method('create')
            ->will($this->returnValue(true));


        $model = new \Magento\Backup\Nomedia($snapshot);

        $rootDir = __DIR__ . DIRECTORY_SEPARATOR . '_files';

        $model = new \Magento\Backup\Nomedia($snapshot);
        $model->setRootDir($rootDir);

        $this->assertTrue($model->create());
        $this->assertEquals(
            array(
                $rootDir . DIRECTORY_SEPARATOR . 'media',
                $rootDir . DIRECTORY_SEPARATOR . 'pub' . DIRECTORY_SEPARATOR . 'media',
            ),
            $snapshot->getIgnorePaths()
        );
    }
}
