<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CustomerBalance_Block_Account_WrapperTest extends PHPUnit_Framework_TestCase
{

    /**
     * @magentoDataFixture Magento/CustomerBalance/_files/history.php
     */
    public function testToHtml()
    {
        $session = Mage::getModel('\Magento\Customer\Model\Session');
        $session->login('customer@example.com', 'password');

        $utility = new Magento_Core_Utility_Layout($this);
        $layout = $utility->getLayoutFromFixture(__DIR__ . '/../../_files/account_wrapper.xml',
            $utility->getLayoutDependencies()
        );
        $layout->getUpdate()->addHandle('magento_customerbalance_info_index')->load();
        $layout->generateXml()->generateElements();
        $layout->addOutputElement('customerbalance.wrapper');
        $html = $layout->getOutput();

        $this->assertContains('<div class="storecredit">', $html);
        $format = '%A<div class="account-balance">%A<table id="customerbalance-history" class="data-table">%A';
        $this->assertStringMatchesFormat($format, $html);
    }
}
