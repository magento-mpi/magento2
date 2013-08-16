<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Module_Declaration_FileResolverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Module_Declaration_FileResolver
     */
    protected $_model;

    protected function setUp()
    {
        $baseDir = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/FileResolver/_files');

        $applicationDirs = $this->getMock('Mage_Core_Model_Dir', array(), array('getDir'), '', false);
        $applicationDirs->expects($this->any())
            ->method('getDir')
            ->will($this->returnValueMap(array(
                array(
                    Mage_Core_Model_Dir::CONFIG,
                    $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR .'etc',
                ),
                array(
                    Mage_Core_Model_Dir::MODULES,
                    $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR .'code',
                ),
            )));
        $this->_model = new Mage_Core_Model_Module_Declaration_FileResolver($applicationDirs);
    }

    public function testGet()
    {
        $expectedResult = array(
            __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/FileResolver/_files/app/code/Module/Four/etc/module.xml'),
            __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/FileResolver/_files/app/code/Module/One/etc/module.xml'),
            __DIR__ . str_replace(
                '/', DIRECTORY_SEPARATOR, '/FileResolver/_files/app/code/Module/Three/etc/module.xml'
            ),
            __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/FileResolver/_files/app/code/Module/Two/etc/module.xml'),
            __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/FileResolver/_files/app/etc/custom/module.xml'),
        );
        $this->assertEquals($expectedResult, $this->_model->get('module.xml', 'global'));
    }

}
