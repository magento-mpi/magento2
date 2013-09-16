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

class Magento_Payment_Block_InfoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture current_store payment/banktransfer/title Bank Method Title
     * @magentoConfigFixture current_store payment/checkmo/title Checkmo Title Of The Method
     */
    public function testGetChildPdfAsArray()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        $block = $layout->createBlock('Magento_Payment_Block_Info', 'block');

        /** @var $paymentInfoBankMagento_Payment_Model_Info  */
        $paymentInfoBank = Mage::getModel('Magento_Payment_Model_Info');
        $paymentInfoBank->setMethodInstance(Mage::getModel('Magento_Payment_Model_Method_Banktransfer'));
        /** @var $childBank Magento_Payment_Block_Info_Instructions */
        $childBank = $layout->addBlock('Magento_Payment_Block_Info_Instructions', 'child.one', 'block');
        $childBank->setInfo($paymentInfoBank)
            ->setArea('adminhtml');

        $nonExpectedHtml = 'non-expected html';
        $childHtml = $layout->addBlock('Magento_Core_Block_Text', 'child.html', 'block');
        $childHtml->setText($nonExpectedHtml);

        /** @var $paymentInfoCheckmo Magento_Payment_Model_Info */
        $paymentInfoCheckmo = Mage::getModel('Magento_Payment_Model_Info');
        $paymentInfoCheckmo->setMethodInstance(Mage::getModel('Magento_Payment_Model_Method_Checkmo'));
        /** @var $childCheckmo Magento_Payment_Block_Info_Checkmo */
        $childCheckmo = $layout->addBlock('Magento_Payment_Block_Info_Checkmo', 'child.just.another', 'block');
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
