<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Multishipping\Helper;

/**
 * Multishipping data helper Test
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Multishipping data helper
     *
     * @var \Magento\Multishipping\Helper\Data
     */
    protected $helper;

    /**
     * Core store config mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfigMock;

    /**
     * Context mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Helper\Context
     */
    protected $contextMock;

    /**
     * Quote mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Sales\Model\Quote
     */
    protected $quoteMock;

    /**
     * Checkout session mock
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Checkout\Model\Session
     */
    protected $checkoutSessionMock;

    public function setUp()
    {
        $this->contextMock = $this->getMock('Magento\Framework\App\Helper\Context', [], [], '', false);
        $this->scopeConfigMock = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->checkoutSessionMock = $this->getMock('\Magento\Checkout\Model\Session', [], [], '', false);
        $this->quoteMock = $this->getMock('\Magento\Sales\Model\Quote', [], [], '', false);

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->helper = $objectManager->getObject(
            'Magento\Multishipping\Helper\Data',
            [
                'context' => $this->contextMock,
                'scopeConfig' => $this->scopeConfigMock,
                'checkoutSession' => $this->checkoutSessionMock
            ]
        );
    }

    public function testGetMaximumQty()
    {
        $maximumQty = 10;
        $this->scopeConfigMock->expects(
            $this->once()
        )->method(
            'getValue'
        )->with(
            \Magento\Multishipping\Helper\Data::XML_PATH_CHECKOUT_MULTIPLE_MAXIMUM_QUANTITY
        )->will(
            $this->returnValue($maximumQty)
        );

        $this->assertEquals($maximumQty, $this->helper->getMaximumQty());
    }

    /**
     * @param bool $result
     * @param bool $quoteHasItems
     * @param bool $isMultiShipping
     * @param bool $hasItemsWithDecimalQty
     * @param bool $validateMinimumAmount
     * @param int $itemsSummaryQty
     * @param int $itemVirtualQty
     * @param int $maximumQty
     * @param bool $hasNominalItems
     * @dataProvider isMultishippingCheckoutAvailableDataProvider
     */
    public function testIsMultishippingCheckoutAvailable(
        $result,
        $quoteHasItems,
        $isMultiShipping,
        $hasItemsWithDecimalQty,
        $validateMinimumAmount,
        $itemsSummaryQty,
        $itemVirtualQty,
        $maximumQty,
        $hasNominalItems
    ) {
        $this->scopeConfigMock->expects(
            $this->once()
        )->method(
            'isSetFlag'
        )->with(
            \Magento\Multishipping\Helper\Data::XML_PATH_CHECKOUT_MULTIPLE_AVAILABLE
        )->will(
            $this->returnValue($isMultiShipping)
        );
        $this->checkoutSessionMock->expects(
            $this->once()
        )->method(
            'getQuote'
        )->will(
            $this->returnValue($this->quoteMock)
        );
        $this->quoteMock->expects($this->once())->method('hasItems')->will($this->returnValue($quoteHasItems));

        $this->quoteMock->expects(
            $this->any()
        )->method(
            'hasItemsWithDecimalQty'
        )->will(
            $this->returnValue($hasItemsWithDecimalQty)
        );
        $this->quoteMock->expects(
            $this->any()
        )->method(
            'validateMinimumAmount'
        )->with(
            true
        )->will(
            $this->returnValue($validateMinimumAmount)
        );
        $this->quoteMock->expects(
            $this->any()
        )->method(
            'getItemsSummaryQty'
        )->will(
            $this->returnValue($itemsSummaryQty)
        );
        $this->quoteMock->expects(
            $this->any()
        )->method(
            'getItemVirtualQty'
        )->will(
            $this->returnValue($itemVirtualQty)
        );
        $this->scopeConfigMock->expects(
            $this->any()
        )->method(
            'getValue'
        )->with(
            \Magento\Multishipping\Helper\Data::XML_PATH_CHECKOUT_MULTIPLE_MAXIMUM_QUANTITY
        )->will(
            $this->returnValue($maximumQty)
        );
        $this->quoteMock->expects($this->any())->method('hasNominalItems')->will($this->returnValue($hasNominalItems));

        $this->assertEquals($result, $this->helper->isMultishippingCheckoutAvailable());
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function isMultishippingCheckoutAvailableDataProvider()
    {
        return [
            [true, false, true, null, null, null, null, null, null],
            [false, false, false, null, null, null, null, null, null],
            [false, true, true, true, null, null, null, null, null],
            [false, true, true, false, false, null, null, null, null],
            [true, true, true, false, true, 2, 1, 3, null],
            [false, true, true, false, true, 1, 2, null, null],
            [false, true, true, false, true, 2, 1, 1, null],
            [true, true, true, false, true, 2, 1, 3, false],
            [false, true, true, false, true, 2, 1, 3, true]
        ];
    }
}
