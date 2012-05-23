<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_CustomerBalance
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_CustomerBalance_Block_Account_WrapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture modules/Enterprise_CustomerBalance/active 1
     * @magentoDataFixture Enterprise/CustomerBalance/_files/history.php
     */
    public function testToHtml()
    {
        $session = new Mage_Customer_Model_Session;
        $session->login('customer@example.com', 'password');

        $utility = new Mage_Core_Utility_Layout($this);
        $layout = $utility->getLayoutFromFixture(__DIR__ . '/../../_files/account_wrapper.xml');
        $layout->getUpdate()->addHandle('enterprise_customerbalance_info_index')->load();
        $layout->generateXml()->generateBlocks();
        $layout->addOutputElement('customerbalance.wrapper');
        $html = $layout->getOutput();

        $this->assertContains('<div class="storecredit">', $html);
        $format = '%A<div class="account-balance">%A<table id="customerbalance-history" class="data-table">%A';
        $this->assertStringMatchesFormat($format, $html);
    }
}
