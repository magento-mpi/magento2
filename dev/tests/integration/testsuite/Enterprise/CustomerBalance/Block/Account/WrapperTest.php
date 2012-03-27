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

/**
 * @group module:Enterprise_CustomerBalance
 */
class Enterprise_CustomerBalance_Block_Account_WrapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Enterprise/CustomerBalance/_files/history.php
     */
    public function testToHtml()
    {
        Mage::getConfig()->setNode('modules/Enterprise_CustomerBalance/active', '1');
        $session = new Mage_Customer_Model_Session;
        $session->login('customer@example.com', 'password');

        $utility = new Mage_Core_Utility_Layout($this);
        $layout = $utility->getLayoutFromFixture(__DIR__ . '/../../_files/account_wrapper.xml');
        $layout->getUpdate()->addHandle('enterprise_customerbalance_info_index')->load();
        $layout->generateXml()->generateBlocks();
        $layout->addOutputElement('customerbalance.wrapper');
        $html = $layout->getOutput();

        $this->assertContains('<div class="storecredit">', $html);

        $balancePos = strpos($html, '<div class="account-balance">');
        $historyPos = strpos($html, '<table id="customerbalance-history" class="data-table">');
        $this->assertGreaterThan(0, $balancePos);
        $this->assertGreaterThan(0, $historyPos);
        $this->assertTrue($historyPos > $balancePos);
    }
}
