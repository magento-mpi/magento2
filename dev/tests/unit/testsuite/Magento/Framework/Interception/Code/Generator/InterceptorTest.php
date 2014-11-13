<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Framework\Interception\Code\Generator;

class InterceptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject
     */
    protected $ioObjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject
     */
    protected $classGeneratorMock;

    /**
     * @var \PHPUnit_Framework_MockObject
     */
    protected $fileResolverMock;

    protected function setUp()
    {
        $this->ioObjectMock = $this->getMock('\Magento\Framework\Code\Generator\Io', [], [], '', false);
        $this->classGeneratorMock = $this->getMock(
            '\Magento\Framework\Code\Generator\CodeGenerator\CodeGeneratorInterface',
            array(),
            array(),
            '',
            false
        );
        $this->fileResolverMock = $this->getMock('Magento\Framework\Code\Generator\FileResolver', [], [], '', false);
    }

    public function testGetDefaultResultClassName()
    {
        // resultClassName should be stdClass_Interceptor
        $model = $this->getMock('\Magento\Framework\Interception\Code\Generator\Interceptor',
            array('_validateData'),
            array('Exception', null, $this->ioObjectMock, $this->classGeneratorMock, $this->fileResolverMock)
        );

        $this->classGeneratorMock->expects($this->once())->method('setName')
            ->will($this->returnValue($this->classGeneratorMock));
        $this->classGeneratorMock->expects($this->once())->method('addProperties')
            ->will($this->returnValue($this->classGeneratorMock));
        $this->classGeneratorMock->expects($this->once())->method('addMethods')
            ->will($this->returnValue($this->classGeneratorMock));
        $this->classGeneratorMock->expects($this->once())->method('setClassDocBlock')
            ->will($this->returnValue($this->classGeneratorMock));
        $this->classGeneratorMock->expects($this->once())->method('generate')
            ->will($this->returnValue('source code example'));
        $model->expects($this->once())->method('_validateData')->will($this->returnValue(true));
        $this->ioObjectMock->expects($this->any())->method('getResultFileName')->with('Exception_Interceptor');
        $this->assertTrue($model->generate());
    }
}
