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
        require_once __DIR__ . '/_files/Sample.php';
        $model = $this->getMock('\Magento\ObjectManager\Code\Generator\Proxy',
            array('_validateData'),
            array('\Magento\ObjectManager\Code\Generator\Sample', null, $this->ioObjectMock, null, null)
        );
        $sampleProxyCode = file_get_contents(__DIR__ . '/_files/SampleProxy.txt');
        
        $this->ioObjectMock->expects($this->once())->method('getResultFileName')
            ->with('\Magento\ObjectManager\Code\Generator\Sample_Proxy')->will($this->returnValue('sample_file.php'));
        $this->ioObjectMock->expects($this->once())->method('writeResultFile')
            ->with('sample_file.php', $sampleProxyCode);

        $model->expects($this->once())->method('_validateData')->will($this->returnValue(true));
        $this->assertTrue($model->generate());
    }


}