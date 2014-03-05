<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\ObjectManager\Code\Generator;

class ProxyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ioObjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $classGeneratorMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $autoloaderMock;

    protected function setUp()
    {
        $this->ioObjectMock = $this->getMock('\Magento\Code\Generator\Io', array(), array(), '', false);
        $this->classGeneratorMock = $this->getMock('\Magento\Code\Generator\CodeGenerator\CodeGeneratorInterface',
            array(), array(), '', false);
        $this->autoloaderMock = $this->getMock('\Magento\Autoload\IncludePath', array(), array(), '', false);
    }

    public function testGenerate()
    {
        $model = $this->getMock('\Magento\ObjectManager\Code\Generator\Proxy',
            array('_validateData'),
            array('Exception', null, $this->ioObjectMock, $this->classGeneratorMock, $this->autoloaderMock)
        );

        $this->classGeneratorMock->expects($this->once())->method('setExtendedClass')->with('\Exception');
        $this->classGeneratorMock->expects($this->once())->method('setName')->with('Exception_Proxy')
            ->will($this->returnValue($this->classGeneratorMock));
        $this->classGeneratorMock->expects($this->once())->method('addProperties')
            ->will($this->returnValue($this->classGeneratorMock));
        $this->classGeneratorMock->expects($this->once())->method('addMethods')
            ->will($this->returnValue($this->classGeneratorMock));
        $this->classGeneratorMock->expects($this->once())->method('setClassDocBlock')
            ->will($this->returnValue($this->classGeneratorMock));
        $this->classGeneratorMock->expects($this->once())->method('generate')
            ->will($this->returnValue('source code example'));

        $this->ioObjectMock->expects($this->once())->method('getResultFileName')
            ->with('Exception_Proxy')->will($this->returnValue('sample_file.php'));
        $this->ioObjectMock->expects($this->once())->method('writeResultFile')
            ->with('sample_file.php', 'source code example');

        $model->expects($this->once())->method('_validateData')->will($this->returnValue(true));
        $this->assertTrue($model->generate());
    }


}