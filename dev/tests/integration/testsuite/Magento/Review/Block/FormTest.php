<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Block;

class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppArea frontend
     */
    public function testCustomerOnForm()
    {
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Customer\Model\Session');
        $service = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Service\V1\CustomerAccountService');
        $customer = $service->authenticate('customer@example.com', 'password');
        $session->setCustomerDataAsLoggedIn($customer);
        $block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Review\Block\Form');
        /** @var \Magento\Framework\Escaper $escaper */
        $escaper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Framework\Escaper');
        $this->assertStringMatchesFormat(
            '%A<input type="text" name="nickname" id="nickname_field" class="input-text"'
                . ' data-validate="{required:true}" value="'
                . $escaper->escapeHtml($customer->getFirstname()) . '" />%A',
            $block->toHtml()
        );
    }
}
