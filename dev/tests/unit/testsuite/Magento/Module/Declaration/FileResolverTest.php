<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Declaration;

class FileResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Module\Declaration\FileResolver
     */
    protected $_model;

    protected function setUp()
    {
        $baseDir = __DIR__ . '/FileResolver/_files';

        $applicationDirs = $this->getMock('Magento\App\Dir', array(), array('getDir'), '', false);
        $applicationDirs->expects($this->any())
            ->method('getDir')
            ->will($this->returnValueMap(array(
                array(
                    \Magento\App\Dir::CONFIG,
                    $baseDir . '/app/etc',
                ),
                array(
                    \Magento\App\Dir::MODULES,
                    $baseDir . '/app/code',
                ),
            )));
        $this->_model = new \Magento\Module\Declaration\FileResolver($applicationDirs);
    }

    public function testGet()
    {
        $expectedResult = array(
            __DIR__ . '/FileResolver/_files/app/code/Module/Four/etc/module.xml',
            __DIR__ . '/FileResolver/_files/app/code/Module/One/etc/module.xml',
            __DIR__ . '/FileResolver/_files/app/code/Module/Three/etc/module.xml',
            __DIR__ . '/FileResolver/_files/app/code/Module/Two/etc/module.xml',
            __DIR__ . '/FileResolver/_files/app/etc/custom/module.xml'
        );
        $this->assertEquals($expectedResult, $this->_model->get('module.xml', 'global'));
    }

}
