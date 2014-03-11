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

    protected function setUp()
    {
        $this->ioObjectMock = $this->getMock('\Magento\Code\Generator\Io', array(), array(), '', false);
    }

    public function testGenerate()
    {
        $model = $this->getMock('\Magento\ObjectManager\Code\Generator\Factory',
            array('_validateData'),
            array('Exception', null, $this->ioObjectMock, null, null)
        );
        $exceptionFactoryCode = file_get_contents(__DIR__ . '/fixture/ExceptionFactory.txt');
        
        $this->ioObjectMock->expects($this->once())->method('getResultFileName')
            ->with('ExceptionFactory')->will($this->returnValue('sample_file.php'));
        $this->ioObjectMock->expects($this->once())->method('writeResultFile')
            ->with('sample_file.php', $exceptionFactoryCode);

        $model->expects($this->once())->method('_validateData')->will($this->returnValue(true));
        $this->assertTrue($model->generate());
    }


}