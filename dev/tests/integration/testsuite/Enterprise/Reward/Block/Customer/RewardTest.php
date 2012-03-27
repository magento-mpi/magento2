<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Reward
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_Reward
 */
class Enterprise_Reward_Block_Customer_RewardTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Enterprise/Reward/_files/history.php
     */
    public function testToHtml()
    {
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->load(1);
        Mage::getSingleton('Mage_Customer_Model_Session')->setCustomer($customer);
        $utility = new Mage_Core_Utility_Layout($this);
        $layout = $utility->getLayoutFromFixture(__DIR__ . '/../../_files/customer_info.xml');
        $layout->getUpdate()->addHandle('enterprise_reward_customer_info')->load();
        $layout->generateXml()->generateBlocks();
        $layout->addOutputElement('customer.reward');

        $html = $layout->getOutput();
        $infoPos = strpos($html, '<div class="box info-box">');
        $historyPos = strpos($html, '<table id="reward-history" class="data-table">');
        $subscriptionPos = strpos($html, 'id="subscribe_updates"');
        $this->assertGreaterThan(0, $infoPos);
        $this->assertGreaterThan(0, $historyPos);
        $this->assertGreaterThan(0, $subscriptionPos);
        $this->assertTrue($infoPos < $historyPos && $historyPos < $subscriptionPos);
    }
}
