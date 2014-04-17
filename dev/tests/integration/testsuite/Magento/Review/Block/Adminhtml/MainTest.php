<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Block\Adminhtml;

class MainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppArea adminhtml
     */
    public function testConstruct()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Customer\Service\V1\CustomerAccountService $service */
        $service = $objectManager->create('Magento\Customer\Service\V1\CustomerAccountService');
        $customer = $service->authenticate('customer@example.com', 'password');
        $request = $objectManager->get('Magento\Framework\App\RequestInterface');
        $request->setParam('customerId', $customer->getId());
        /** @var \Magento\View\LayoutInterface $layout */
        $layout = $objectManager->get('Magento\View\LayoutInterface');
        $block = $layout->createBlock('Magento\Review\Block\Adminhtml\Main');
        $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
        /** @var \Magento\Escaper $escaper */
        $escaper = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Escaper');
        $this->assertStringMatchesFormat(
            '%A' . __('All Reviews of Customer `%1`', $escaper->escapeHtml($customerName)) . '%A',
            $block->getHeaderHtml()
        );
    }
}
