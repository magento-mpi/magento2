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

namespace Magento\CustomerBalance\Block\Account;

class WrapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @magentoDataFixture Magento/CustomerBalance/_files/history.php
     */
    public function testToHtml()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
            ->loadArea('frontend');
        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Session', array($logger));
        $service = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Service\V1\CustomerAccountService');
        $customer = $service->authenticate('customer@example.com', 'password');
        $session->setCustomerDtoAsLoggedIn($customer);

        $utility = new \Magento\Core\Utility\Layout($this);
        $layout = $utility->getLayoutFromFixture(__DIR__ . '/../../_files/magento_customerbalance_info_index.xml',
            $utility->getLayoutDependencies()
        );
        $layout->getUpdate()->addHandle('magento_customerbalance_info_index')->load();
        $layout->generateXml()->generateElements();
        $layout->addOutputElement('customerbalance.wrapper');
        $html = $layout->getOutput();

        $this->assertContains('<div class="storecredit">', $html);
        $format = '%A<div class="block balance">%A'
            . '<table id="customerbalance-history" class="data table balance history">%A';
        $this->assertStringMatchesFormat($format, $html);
    }
}
