<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Pbridge\Block\Iframe;

class AbstractIframeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\View\Layout */
    protected $_layout;

    /** @var \Magento\Customer\Model\Session */
    protected $_customerSession;

    /** @var \Magento\Checkout\Model\Session */
    protected $_checkoutSession;

    /** @var \Magento\Framework\App\Http\Context */
    protected $_httpContext;

    /** @var \Magento\Pbridge\Block\Iframe\ExtendsAbstractIframe */
    protected $_block;

    protected function setUp()
    {
        $this->_checkoutSession = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false);
        $this->_customerSession = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);
        $this->_httpContext = $this->getMock('Magento\Framework\App\Http\Context', [], [], '', false);

        $this->_layout = $this->getMock('Magento\Framework\View\Layout', [], [], '', false);
        $context = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $context->expects($this->any())
            ->method('getLayout')
            ->will($this->returnValue($this->_layout));

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_block = $helper->getObject(
            'Magento\Pbridge\Block\Iframe\ExtendsAbstractIframe',
            [
                'context' => $context,
                'customerSession' => $this->_customerSession,
                'checkoutSession' => $this->_checkoutSession,
                'httpContext' => $this->_httpContext
            ]
        );
    }

    public function testGetQuote()
    {
        $this->_checkoutSession->expects($this->any())
            ->method('getQuote')
            ->will($this->returnValue('quote'));
        $this->assertEquals('quote', $this->_block->getQuote());
    }

    public function testGetIframeHeight()
    {
        $this->assertEquals(500, $this->_block->getIframeHeight());
    }

    public function testGetIframeBlock()
    {
        $iframeBlock = new \Magento\Framework\Object();
        $this->_layout->expects($this->atLeastOnce())
            ->method('createBlock')
            ->with('Magento\Framework\View\Element\Template')
            ->will($this->returnValue($iframeBlock));

        $result = $this->_block->getIframeBlock();
        $this->assertEquals('Magento_Pbridge::iframe.phtml', $result->getTemplate());
        $this->assertEquals(500, $result->getIframeHeight());
        $this->assertEquals('source_url', $result->getSourceUrl());
    }

    /**
     * @dataProvider testGetCustomerEmailProvider
     */
    public function testGetCustomerEmail($customerEmail, $quoteCustomerEmail, $expected)
    {
        $this->_httpContext->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue(true));

        $customer = new \Magento\Framework\Object(['email' => $customerEmail]);
        $this->_customerSession->expects($this->once())
            ->method('getCustomer')
            ->will($this->returnValue($customer));

        $quote = new \Magento\Framework\Object(['customer_email' => $quoteCustomerEmail]);
        $this->_checkoutSession->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue($quote));

        $this->assertEquals($expected, $this->_block->getCustomerEmail());
    }

    public function testGetCustomerEmailProvider()
    {
        return [
            ['customer@example.com', 'quote@example.com', 'customer@example.com'],
            [null, 'quote@example.com', 'quote@example.com'],
            ['customer@example.com', null, 'customer@example.com'],
            [null, null, null]
        ];
    }
}
