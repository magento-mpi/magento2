<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Payment\Block;

class InfoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture current_store payment/banktransfer/title Bank Method Title
     * @magentoConfigFixture current_store payment/checkmo/title Checkmo Title Of The Method
     */
    public function testGetChildPdfAsArray()
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Layout');
        $block = $layout->createBlock('Magento\Payment\Block\Info', 'block');

        /** @var $paymentInfoBankMagento_Payment_Model_Info  */
        $paymentInfoBank = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Payment\Model\Info');
        $paymentInfoBank->setMethodInstance(\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Payment\Model\Method\Banktransfer'));
        /** @var $childBank \Magento\Payment\Block\Info\Instructions */
        $childBank = $layout->addBlock('Magento\Payment\Block\Info\Instructions', 'child.one', 'block');
        $childBank->setInfo($paymentInfoBank)
            ->setArea('adminhtml');

        $nonExpectedHtml = 'non-expected html';
        $childHtml = $layout->addBlock('Magento\Core\Block\Text', 'child.html', 'block');
        $childHtml->setText($nonExpectedHtml);

        /** @var $paymentInfoCheckmo \Magento\Payment\Model\Info */
        $paymentInfoCheckmo = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Payment\Model\Info');
        $paymentInfoCheckmo->setMethodInstance(\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Payment\Model\Method\Checkmo'));
        /** @var $childCheckmo \Magento\Payment\Block\Info\Checkmo */
        $childCheckmo = $layout->addBlock('Magento\Payment\Block\Info\Checkmo', 'child.just.another', 'block');
        $childCheckmo->setInfo($paymentInfoCheckmo)
            ->setArea('adminhtml');

        $pdfArray = $block->getChildPdfAsArray();

        $this->assertInternalType('array', $pdfArray);
        $this->assertCount(2, $pdfArray);
        $text = implode('', $pdfArray);
        $this->assertContains('Bank Method Title', $text);
        $this->assertContains('Checkmo Title Of The Method', $text);
        $this->assertNotContains($nonExpectedHtml, $text);
    }
}
