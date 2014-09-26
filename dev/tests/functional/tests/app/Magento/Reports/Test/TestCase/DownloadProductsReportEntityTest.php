<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\TestCase;

use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Downloadable\Test\Page\DownloadableCustomerProducts;
use Mtf\TestCase\Injectable;
use Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Test Creation for DownloadProductsReportEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create customer.
 * 2. Create downloadable product.
 * 3. Place order.
 * 4. Create invoice.
 * 5. Go to customer account > My Downloads and click download link.
 *
 * Steps:
 * 1. Open Backend.
 * 2. Go to Reports> Products> Downloads.
 * 3. Perform all assertions.
 *
 * @group Reports_(MX)
 * @ZephyrId MAGETWO-28823
 */
class DownloadProductsReportEntityTest extends Injectable
{
    /**
     * Customer Account index page
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccount;

    /**
     * Downloadable Customer Products page
     *
     * @var DownloadableCustomerProducts
     */
    protected $customerProducts;

    /**
     * Inject pages
     *
     * @param CustomerAccountIndex $customerAccount
     * @param DownloadableCustomerProducts $customerProducts
     * @return void
     */
    public function __inject(CustomerAccountIndex $customerAccount, DownloadableCustomerProducts $customerProducts)
    {
        $this->customerAccount = $customerAccount;
        $this->customerProducts = $customerProducts;
    }

    /**
     * Order downloadable product
     *
     * @param OrderInjectable $order
     * @param string $downloads
     * @return void
     */
    public function test(OrderInjectable $order, $downloads)
    {
        // Preconditions
        $order->persist();
        $invoice = $this->objectManager->create('Magento\Sales\Test\TestStep\CreateInvoiceStep', ['order' => $order]);
        $invoice->run();
        $this->openDownloadableLink($order, (int)$downloads);
    }

    /**
     * Open Downloadable Link
     *
     * @param OrderInjectable $order
     * @param int $downloads
     * @return void
     */
    protected function openDownloadableLink(OrderInjectable $order, $downloads)
    {
        $customerLogin = $this->objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $order->getDataFieldConfig('customer_id')['source']->getCustomer()]
        );
        $customerLogin->run();
        $this->customerAccount->getAccountMenuBlock()->openMenuItem('My Downloadable Products');
        foreach ($order->getEntityId()['products'] as $product) {
            foreach ($product->getDownloadableLinks()['downloadable']['link'] as $link) {
                for ($i = 0; $i < $downloads; $i++) {
                    $this->customerProducts->getMainBlock()->openLink($link['title']);
                }
            }
        }
    }
}
