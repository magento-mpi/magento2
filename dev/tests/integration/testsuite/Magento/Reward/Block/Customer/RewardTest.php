<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Block\Customer;

class RewardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Reward/_files/history.php
     */
    public function testToHtml()
    {
        $customer = \Mage::getModel('Magento\Customer\Model\Customer');
        $customer->load(1);

        \Mage::getSingleton('Magento\Customer\Model\Session')->setCustomer($customer);

        $utility = new \Magento\Core\Utility\Layout($this);
        $layout = $utility->getLayoutFromFixture(__DIR__ . '/../../_files/customer_info.xml',
            $utility->getLayoutDependencies());

        $layout->getUpdate()->addHandle('magento_reward_customer_info')->load();
        $layout->generateXml()->generateElements();
        $layout->addOutputElement('customer.reward');

        $format = '%A<div class="box info-box">%A<table id="reward-history" class="data-table">%A'
            . 'id="subscribe_updates"%A';
        $this->assertStringMatchesFormat($format, $layout->getOutput());
    }
}
