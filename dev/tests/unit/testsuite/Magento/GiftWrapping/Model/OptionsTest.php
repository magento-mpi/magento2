<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model;

class OptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftWrapping\Model\Options
     */
    protected $subject;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->subject = $objectManagerHelper->getObject('\Magento\GiftWrapping\Model\Options');
    }

    public function testSetDataObjectIfItemNotMagentoObject()
    {
        $itemMock = $this->getMock('\stdClass', [], [], '', false);
        $this->assertEquals($this->subject, $this->subject->setDataObject($itemMock));
    }

    public function testSetDataObjectIfItemHasNotWrappingOptions()
    {
        $itemMock = $this->getMock('\Magento\Framework\Object', ['getGiftwrappingOptions'], [], '', false);
        $itemMock->expects($this->once())->method('getGiftwrappingOptions')->will($this->returnValue(null));
        $this->assertEquals($this->subject, $this->subject->setDataObject($itemMock));
    }

    public function testSetDataObjectSuccess()
    {
        $wrappingOptions = serialize(['option' => 'wrapping_option']);
        $itemMock = $this->getMock('\Magento\Framework\Object', ['getGiftwrappingOptions'], [], '', false);
        $itemMock->expects($this->exactly(2))
            ->method('getGiftwrappingOptions')
            ->will($this->returnValue($wrappingOptions));
        $this->assertEquals($this->subject, $this->subject->setDataObject($itemMock));
    }

    public function testUpdateSuccess()
    {
        $wrappingOptions = serialize(['option' => 'wrapping_option']);
        $itemMock = $this->getMock(
            '\Magento\Framework\Object',
            ['getGiftwrappingOptions', 'setGiftwrappingOptions'],
            [],
            '',
            false
        );
        $itemMock->expects($this->exactly(2))
            ->method('getGiftwrappingOptions')
            ->will($this->returnValue($wrappingOptions));
        $this->assertEquals($this->subject, $this->subject->setDataObject($itemMock));

        $itemMock->expects($this->once())
            ->method('setGiftwrappingOptions')
            ->with($wrappingOptions)
            ->will($this->returnValue($wrappingOptions));
        $this->assertEquals($this->subject, $this->subject->update());
    }
}
 