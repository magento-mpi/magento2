<?php
/**
 * Test for \Magento\Paypal\Block\Payment\Form\Billing\Agreement
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Paypal\Block\Payment\Form\Billing;

class AgreementTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Paypal\Block\Payment\Form\Billing\Agreement */
    protected $_block;

    protected function setUp()
    {
        $quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Sales\Model\Resource\Quote\Collection'
        )->getFirstItem();
        /** @var \Magento\Framework\View\LayoutInterface $layout */
        $layout = $this->getMockBuilder('Magento\Framework\View\LayoutInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $layout->expects(
            $this->once()
        )->method(
            'getBlock'
        )->will(
            $this->returnValue(new \Magento\Framework\Object(['quote' => $quote]))
        );
        $layout->expects($this->once())->method('getParentName')->will($this->returnValue('billing_agreement_form'));

        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Paypal\Block\Payment\Form\Billing\Agreement'
        );
        $this->_block->setLayout($layout);
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/quote_with_customer.php
     * @magentoDataFixture Magento/Paypal/_files/billing_agreement.php
     */
    public function testGetBillingAgreements()
    {
        $billingAgreements = $this->_block->getBillingAgreements();
        $this->assertEquals(1, count($billingAgreements));
        $this->assertEquals('REF-ID-TEST-678', array_shift($billingAgreements));
    }
}
