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
        $block = Mage::app()->getLayout()->createBlock('Mage_Payment_Block_Info');
        $structure = Mage::getModel('Magento_Data_Structure');
        $layout = Mage::getModel('Mage_Core_Model_Layout', array('structure' => $structure));
        $layout->addBlock($block, 'block');

        $paymentInfoBank = Mage::getModel('Mage_Payment_Model_Info');
        $paymentInfoBank->setMethodInstance(Mage::getModel('Mage_Payment_Model_Method_Banktransfer'));
        $childBank = $layout->addBlock('Mage_Payment_Block_Info_Instructions', 'child.one', 'block');
        $childBank->setInfo($paymentInfoBank)
            ->setArea('adminhtml');

        $nonExpectedHtml = 'non-expected html';
        $childHtml = $layout->addBlock('Mage_Core_Block_Text', 'child.html', 'block');
        $childHtml->setText($nonExpectedHtml);

        $paymentInfoCheckmo = Mage::getModel('Mage_Payment_Model_Info');
        $paymentInfoCheckmo->setMethodInstance(Mage::getModel('Mage_Payment_Model_Method_Checkmo'));
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
