<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\ObjectManager\Code\Generator;

class FactoryTest extends \PHPUnit_Framework_TestCase
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
        $classProperties = array (
            0 =>
                array (
                    'name' => '_objectManager',
                    'visibility' => 'protected',
                    'docblock' =>
                        array (
                            'shortDescription' => 'Object Manager instance',
                            'tags' =>
                                array (
                                    0 =>
                                        array (
                                            'name' => 'var',
                                            'description' => '\\Magento\\ObjectManager',
                                        ),
                                ),
                        ),
                ),
            1 =>
                array (
                    'name' => '_instanceName',
                    'visibility' => 'protected',
                    'docblock' =>
                        array (
                            'shortDescription' => 'Instance name to create',
                            'tags' =>
                                array (
                                    0 =>
                                        array (
                                            'name' => 'var',
                                            'description' => 'string',
                                        ),
                                ),
                        ),
                ),
        );

        $model = $this->getMock('\Magento\ObjectManager\Code\Generator\Factory',
            array('_validateData'),
            array('Exception', null, $this->ioObjectMock, $this->classGeneratorMock, $this->autoloaderMock)
        );
        $this->classGeneratorMock->expects($this->once())->method('setName')->with('ExceptionFactory')
            ->will($this->returnValue($this->classGeneratorMock));
        $this->classGeneratorMock->expects($this->once())->method('addProperties')->with($classProperties)
            ->will($this->returnValue($this->classGeneratorMock));
        $this->classGeneratorMock->expects($this->once())->method('addMethods')
            ->will($this->returnValue($this->classGeneratorMock));
        $this->classGeneratorMock->expects($this->once())->method('setClassDocBlock')
            ->will($this->returnValue($this->classGeneratorMock));
        $this->classGeneratorMock->expects($this->once())->method('generate')
            ->will($this->returnValue('source code example'));

        $this->ioObjectMock->expects($this->once())->method('getResultFileName')
            ->with('ExceptionFactory')->will($this->returnValue('sample_file.php'));
        $this->ioObjectMock->expects($this->once())->method('writeResultFile')
            ->with('sample_file.php', 'source code example');

        $model->expects($this->once())->method('_validateData')->will($this->returnValue(true));
        $this->assertTrue($model->generate());
    }


}