<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesRule\Model;

class ValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SalesRule\Model\Validator|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMock(
            'Magento\SalesRule\Model\Validator',
            array('_getRules', '_getItemOriginalPrice', '_getItemBaseOriginalPrice', '__wakeup'),
            array(),
            '',
            false
        );
        $this->_model->expects($this->any())
            ->method('_getRules')
            ->will($this->returnValue(array()));
        $this->_model->expects($this->any())
            ->method('_getItemOriginalPrice')
            ->will($this->returnValue(1));
        $this->_model->expects($this->any())
            ->method('_getItemBaseOriginalPrice')
            ->will($this->returnValue(1));
    }

    /**
     * @return \Magento\Sales\Model\Quote\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getQuoteItemMock()
    {
        $fixturePath = __DIR__ . '/_files/';
        $itemDownloadable = $this->getMock(
            'Magento\Sales\Model\Quote\Item',
            array('getAddress', '__wakeup'),
            array(),
            '',
            false
        );
        $itemDownloadable->expects($this->any())
            ->method('getAddress')
            ->will($this->returnValue(new \stdClass()));

        $itemSimple = $this->getMock(
            'Magento\Sales\Model\Quote\Item',
            array('getAddress', '__wakeup'),
            array(),
            '',
            false
        );
        $itemSimple->expects($this->any())
            ->method('getAddress')
            ->will($this->returnValue(new \stdClass()));

        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $this->getMock(
            'Magento\Sales\Model\Quote',
            array('hasNominalItems', '__wakeup'),
            array(),
            '',
            false
        );
        $quote->expects($this->any())
            ->method('hasNominalItems')
            ->will($this->returnValue(false));

        $itemData = include($fixturePath . 'quote_item_downloadable.php');
        $itemDownloadable->addData($itemData);
        $quote->addItem($itemDownloadable);

        $itemData = include($fixturePath . 'quote_item_simple.php');
        $itemSimple->addData($itemData);
        $quote->addItem($itemSimple);

        return $itemDownloadable;
    }

    public function testCanApplyRules()
    {
        $item = $this->_getQuoteItemMock();

        $quote = $item->getQuote();
        $quote->setItemsQty(2);
        $quote->setVirtualItemsQty(1);

        $this->assertTrue($this->_model->canApplyRules($item));

        $quote->setItemsQty(2);
        $quote->setVirtualItemsQty(2);

        $this->assertTrue($this->_model->canApplyRules($item));

        return true;
    }

    public function testProcessFreeShipping()
    {
        $item = $this->getMock('Magento\Sales\Model\Quote\Item', array('getAddress', '__wakeup'), array(), '', false);
        $item->expects($this->once())
            ->method('getAddress')
            ->will($this->returnValue(true));

        $this->assertInstanceOf('Magento\SalesRule\Model\Validator', $this->_model->processFreeShipping($item));

        return true;
    }

    public function testProcessWhenItemPriceIsNegativeRulesAreNotApplied()
    {
        $negativePrice = -1;

        // 1. Get mocks
        /** @var \Magento\SalesRule\Model\Validator|\PHPUnit_Framework_MockObject_MockObject $validator */
        $validator = $this->getMock(
            'Magento\SalesRule\Model\Validator', array('applyRules', '__wakeup'), array(), '', false
        );

        /** @var \Magento\Sales\Model\Quote\Item\AbstractItem|\PHPUnit_Framework_MockObject_MockObject $item */
        $item = $this->getMock('Magento\Sales\Model\Quote\Item', array('__wakeup'), array(), '', false);

        // 2. Set fixtures
        $item->setDiscountCalculationPrice($negativePrice);
        $item->setData('calculation_price', $negativePrice);

        // 3. Set expectations
        $validator->expects($this->never())->method('applyRules');

        // 4. Run tested method
        $validator->process($item);
    }

    public function testProcessWhenItemPriceIsNegativeDiscountsAreZeroed()
    {
        $negativePrice = -1;
        $nonZeroDiscount = 123;

        // 1. Get mocks
        /** @var \Magento\SalesRule\Model\Validator|\PHPUnit_Framework_MockObject_MockObject $validator */
        $validator = $this->getMock(
            'Magento\SalesRule\Model\Validator', array('applyRules', '__wakeup'), array(), '', false
        );

        /** @var \Magento\Sales\Model\Quote\Item\AbstractItem|\PHPUnit_Framework_MockObject_MockObject $item */
        $item = $this->getMock('Magento\Sales\Model\Quote\Item', array('__wakeup'), array(), '', false);

        // 2. Set fixtures
        $item->setDiscountCalculationPrice($negativePrice);
        $item->setData('calculation_price', $negativePrice);

        // Discounts that could be set before running tested method
        $item->setDiscountAmount($nonZeroDiscount);
        $item->setBaseDiscountAmount($nonZeroDiscount);
        $item->setDiscountPercent($nonZeroDiscount);

        // 3. Run tested method
        $validator->process($item);

        // 4. Check expected result
        $this->assertEquals(0, $item->getDiscountAmount());
        $this->assertEquals(0, $item->getBaseDiscountAmount());
        $this->assertEquals(0, $item->getDiscountPercent());
    }

    public function testProcessAppliedRuleIdsAreSet()
    {
        $positivePrice = 1;
        $previouslySetRuleIds = array(1, 2, 4);
        $exampleRuleIds = array(1, 2, 3, 5);
        $expectedRuleIds = '1,2,3,5';
        $expectedMergedRuleIds = '1,2,3,4,5';

        // 1. Get mocks
        /** @var \Magento\SalesRule\Model\Validator|\PHPUnit_Framework_MockObject_MockObject $validator */
        $validator = $this->getMock(
            'Magento\SalesRule\Model\Validator', array('applyRules', '__wakeup'), array(), '', false
        );

        /** @var \Magento\Sales\Model\Quote\Item\AbstractItem|\PHPUnit_Framework_MockObject_MockObject $item */
        $item = $this->getMock(
            'Magento\Sales\Model\Quote\Item', array('getAddress', 'getQuote', '__wakeup'), array(), '', false
        );

        /** @var \Magento\Sales\Model\Quote\Address|\PHPUnit_Framework_MockObject_MockObject $address */
        $address = $this->getMock(
            'Magento\Sales\Model\Quote\Address', array('__wakeup'), array(), '', false
        );
        $item->expects($this->any())->method('getAddress')->will($this->returnValue($address));

        /** @var \Magento\Sales\Model\Quote|\PHPUnit_Framework_MockObject_MockObject $quote */
        $quote = $this->getMock(
            'Magento\Sales\Model\Quote', array('__wakeup'), array(), '', false
        );
        $item->expects($this->any())->method('getQuote')->will($this->returnValue($quote));

        // 2. Set fixtures
        $item->setDiscountCalculationPrice($positivePrice);
        $item->setData('calculation_price', $positivePrice);
        $validator->expects($this->any())->method('applyRules')->will($this->returnValue($exampleRuleIds));
        $address->setAppliedRuleIds($previouslySetRuleIds);
        $quote->setAppliedRuleIds($previouslySetRuleIds);

        // 3. Run tested method
        $validator->process($item);

        // 4. Check expected result
        $this->assertEquals($expectedRuleIds, $item->getAppliedRuleIds());

        $arrayAddress = explode(',', $item->getAddress()->getAppliedRuleIds());
        sort($arrayAddress);
        $this->assertEquals($expectedMergedRuleIds, join(',', $arrayAddress));

        $arrayQuote = explode(',', $item->getQuote()->getAppliedRuleIds());
        sort($arrayQuote);
        $this->assertEquals($expectedMergedRuleIds, join(',', $arrayQuote));
    }
}
