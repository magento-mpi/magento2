<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Block\Customer;

class LinkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManagerHelper;

    protected function setUp()
    {
        $this->_objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    public function testToHtml()
    {
        /** @var \Magento\AdvancedCheckout\Helper\Data|\PHPUnit_Framework_MockObject_MockObject $customerHelper */
        $customerHelper = $this->getMockBuilder(
            'Magento\AdvancedCheckout\Helper\Data'
        )->disableOriginalConstructor()->getMock();

        /** @var \Magento\Invitation\Block\Link $block */
        $block = $this->_objectManagerHelper->getObject(
            'Magento\AdvancedCheckout\Block\Customer\Link',
            ['customerHelper' => $customerHelper]
        );

        $customerHelper->expects($this->once())->method('isSkuApplied')->will($this->returnValue(false));

        $this->assertEquals('', $block->toHtml());
    }
}
