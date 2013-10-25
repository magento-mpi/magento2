<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Module\Declaration;

class FileResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Module\Declaration\FileResolver
     */
    protected $_model;

    protected function setUp()
    {
        $baseDir = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, '/FileResolver/_files');

        $applicationDirs = $this->getMock('Magento\App\Dir', array(), array('getDir'), '', false);
        $applicationDirs->expects($this->any())
            ->method('getDir')
            ->will($this->returnValueMap(array(
                array(
                    \Magento\App\Dir::CONFIG,
                    $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR .'etc',
                ),
                array(
                    \Magento\App\Dir::MODULES,
                    $baseDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR .'code',
                ),
            )));
        $this->_model = new \Magento\App\Module\Declaration\FileResolver($applicationDirs);
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
