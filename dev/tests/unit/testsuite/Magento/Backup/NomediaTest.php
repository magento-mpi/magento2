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

class Magento_Backup_NomediaTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $snapshot = $this->getMock(
            'Magento_Backup_Snapshot',
            array('create')
        );
        $snapshot->expects($this->any())
            ->method('create')
            ->will($this->returnValue(true));


        $model = new Magento_Backup_Nomedia($snapshot);

        $rootDir = __DIR__ . DIRECTORY_SEPARATOR . '_files';
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
