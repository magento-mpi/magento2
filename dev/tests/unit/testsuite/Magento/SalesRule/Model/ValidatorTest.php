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

    public function testProcess()
    {
        $item = $this->getMock('Magento\Sales\Model\Quote\Item', array('getAddress', '__wakeup'), array(), '', false);
        $item->expects($this->once())
            ->method('getAddress')
            ->will($this->returnValue(true));
        $item->setDiscountCalculationPrice(-1);
        $item->setCalculationPrice(1);

        $quote = $this->getMock('Magento\Sales\Model\Quote', array('__wakeup'), array(), '', false);
        $item->setQuote($quote);

        $this->assertInstanceOf('Magento\SalesRule\Model\Validator', $this->_model->process($item));

        return true;
    }
}
