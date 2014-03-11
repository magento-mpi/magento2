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

    protected function setUp()
    {
        $this->ioObjectMock = $this->getMock('\Magento\Code\Generator\Io', array(), array(), '', false);
    }

    public function testGenerate()
    {

        $model = $this->getMock('\Magento\ObjectManager\Code\Generator\Proxy',
            array('_validateData'),
            array('Exception', null, $this->ioObjectMock, null, null)
        );
        $exceptionProxyCode = file_get_contents(__DIR__ . '/fixture/ExceptionProxy.txt');
        
        $this->ioObjectMock->expects($this->once())->method('getResultFileName')
            ->with('Exception_Proxy')->will($this->returnValue('sample_file.php'));
        $this->ioObjectMock->expects($this->once())->method('writeResultFile')
            ->with('sample_file.php', $exceptionProxyCode);

        $model->expects($this->once())->method('_validateData')->will($this->returnValue(true));
        $this->assertTrue($model->generate());
    }


}