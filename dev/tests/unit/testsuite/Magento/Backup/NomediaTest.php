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

class NomediaTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $filesystem = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $backupFactory = $this->getMock('Magento\Backup\Factory', array(), array(), '', false);
        $snapshot = $this->getMock('Magento\Backup\Snapshot', array('create'), array($filesystem, $backupFactory));
        $snapshot->expects($this->any())
            ->method('create')
            ->will($this->returnValue(true));


        $model = new \Magento\Backup\Nomedia($snapshot);

        $rootDir = __DIR__ . '/_files';

        $model = new \Magento\Backup\Nomedia($snapshot);
        $model->setRootDir($rootDir);

        $this->assertTrue($model->create());
        $this->assertEquals(
            array(
                $rootDir . '/media',
                $rootDir . '/pub/media',
            ),
            $snapshot->getIgnorePaths()
        );
    }
}
