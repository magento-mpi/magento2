<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata;

use Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\Reader
     */
    protected $reader;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $defaultReaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $selectReaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $optionMock;

    protected function setUp()
    {
        $this->defaultReaderMock =
            $this->getMock('Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ReaderInterface');
        $this->selectReaderMock =
            $this->getMock('Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\ReaderInterface');
        $this->optionMock =
            $this->getMock('Magento\Catalog\Model\Product\Option', ['getType', '__wakeup'], [], '', false);
        $this->reader = new \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option\Metadata\Reader(
            [
                'default' => $this->defaultReaderMock,
                'select' => $this->selectReaderMock
            ]
        );
    }

    public function testReadOptionWithTypeText()
    {
        $this->optionMock->expects($this->once())->method('getType')->will($this->returnValue('text'));
        $this->defaultReaderMock->expects($this->once())->method('read')->with($this->optionMock);
        $this->reader->read($this->optionMock);
    }

    public function testReadOptionWithTypeSelect()
    {
        $this->optionMock->expects($this->once())->method('getType')->will($this->returnValue('select'));
        $this->selectReaderMock->expects($this->once())->method('read')->with($this->optionMock);
        $this->reader->read($this->optionMock);
    }
}
