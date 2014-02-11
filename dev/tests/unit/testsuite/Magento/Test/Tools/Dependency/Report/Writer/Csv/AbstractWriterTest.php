<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Test\Tools\Dependency\Report\Writer\Csv;

class AbstractWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\Dependency\Report\Writer\Csv\AbstractWriter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $writer;

    /**
     * @var \Magento\File\Csv|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $csvMock;

    protected function setUp()
    {
        $this->csvMock = $this->getMock('Magento\File\Csv');

        $this->writer = $this->getMockForAbstractClass('Magento\Tools\Dependency\Report\Writer\Csv\AbstractWriter', [
            'writer' => $this->csvMock,
        ]);
    }

    public function testWrite()
    {
        $filename = 'some_filename';
        $configMock = $this->getMock('Magento\Tools\Dependency\Config', [], [], '', false);
        $preparedData = ['foo', 'baz', 'bar'];

        $this->writer->expects($this->once())->method('prepareData')->with($configMock)
            ->will($this->returnValue($preparedData));
        $this->csvMock->expects($this->once())->method('saveData')->with($filename, $preparedData);

        $this->writer->write($filename, $configMock);
    }
}
