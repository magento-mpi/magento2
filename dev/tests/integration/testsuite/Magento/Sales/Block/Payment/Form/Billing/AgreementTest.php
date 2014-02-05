<?php
/**
 * Test for \Magento\Sales\Block\Payment\Form\Billing\Agreement
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Payment\Form\Billing;

class AgreementTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Sales\Block\Payment\Form\Billing\Agreement */
    protected $_block;

    protected function setUp()
    {
        $quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Model\Resource\Quote\Collection')
            ->getFirstItem();
        /** @var \Magento\View\LayoutInterface $layout */
        $layout = $this->getMockBuilder('Magento\View\LayoutInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $layout->expects($this->once())
            ->method('getBlock')
            ->will($this->returnValue(new \Magento\Object(['quote' => $quote])));
        $layout->expects($this->once())
            ->method('getParentName')
            ->will($this->returnValue('billing_agreement_form'));

        $this->_block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Sales\Block\Payment\Form\Billing\Agreement');
        $this->_block->setLayout($layout);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Sales/_files/quote_with_customer.php
     * @magentoDataFixture Magento/Sales/_files/billing_agreement.php
     */
    public function testGetBillingAgreements()
    {
        $billingAgreements = $this->_block->getBillingAgreements();
        $this->assertEquals(1, count($billingAgreements));
    }
}