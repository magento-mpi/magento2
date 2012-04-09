<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Payment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Payment_Block_InfoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture current_store payment/banktransfer/title Bank Method Title
     * @magentoConfigFixture current_store payment/checkmo/title Checkmo Title Of The Method
     */
    public function testGetChildPdfAsArray()
    {
        $block = new Mage_Payment_Block_Info;
        $layout = new Mage_Core_Model_Layout;
        $layout->addBlock($block, 'block');

        $paymentInfoBank = new Mage_Payment_Model_Info;
        $paymentInfoBank->setMethodInstance(new Mage_Payment_Model_Method_Banktransfer);
        $childBank = $layout->addBlock('Mage_Payment_Block_Info_Banktransfer', 'child.one', 'block');
        $childBank->setInfo($paymentInfoBank)
            ->setArea('adminhtml');

        $nonExpectedHtml = 'non-expected html';
        $childHtml = $layout->addBlock('Mage_Core_Block_Text', 'child.html', 'block');
        $childHtml->setText($nonExpectedHtml);

        $paymentInfoCheckmo = new Mage_Payment_Model_Info;
        $paymentInfoCheckmo->setMethodInstance(new Mage_Payment_Model_Method_Checkmo);
        $childCheckmo = $layout->addBlock('Mage_Payment_Block_Info_Checkmo', 'child.just.another', 'block');
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
