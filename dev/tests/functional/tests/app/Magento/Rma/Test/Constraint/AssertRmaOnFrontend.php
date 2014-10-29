<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Rma\Test\Page\CustomerAccountRmaIndex;
use Magento\Rma\Test\Page\CustomerAccountRmaView;
use Mtf\Fixture\FixtureInterface;
use Magento\Rma\Test\Fixture\Rma;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Assert that rma is correct display on frontend (MyAccount - My Returns).
 */
class AssertRmaOnFrontend extends AbstractAssertForm
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that rma is correct display on frontend (MyAccount - My Returns):
     * - status on rma history page
     * - details and items on rma view page
     *
     * @param Rma $rma
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerAccountRmaIndex $customerAccountRmaIndex
     * @param CustomerAccountRmaView $customerAccountRmaView
     * @return void
     */
    public function processAssert(
        Rma $rma,
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        CustomerAccountRmaIndex $customerAccountRmaIndex,
        CustomerAccountRmaView $customerAccountRmaView
    ) {
        /** @var OrderInjectable $order */
        $order = $rma->getDataFieldConfig('order_id')['source']->getOrder();
        /** @var CustomerInjectable $customer */
        $customer = $order->getDataFieldConfig('customer_id')['source']->getCustomer();

        $this->login($customer);
        $cmsIndex->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Returns');

        $fixtureRmaStatus = $rma->getStatus();
        $pageRmaData = $customerAccountRmaIndex->getRmaHistory()->getRmaRow($rma)->getData();
        \PHPUnit_Framework_Assert::assertEquals(
            $fixtureRmaStatus,
            $pageRmaData['status'],
            "\nWrong display status of rma."
            . "\nExpected: " . $fixtureRmaStatus
            . "\nActual: " . $pageRmaData['status']
        );

        $customerAccountRmaIndex->getRmaHistory()->getRmaRow($rma)->clickView();
        $pageItemsData = $this->sortDataByPath(
            $customerAccountRmaView->getRmaView()->getRmaItems()->getData(),
            '::sku'
        );
        $fixtureItemsData = $this->sortDataByPath(
            $this->getRmaItems($rma),
            '::sku'
        );
        \PHPUnit_Framework_Assert::assertEquals($fixtureItemsData, $pageItemsData);
    }

    /**
     * Login customer.
     *
     * @param CustomerInjectable $customer
     * @return void
     */
    protected function login(CustomerInjectable $customer)
    {
        $this->objectManager->create(
            '\Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        )->run();
    }

    /**
     * Get rma items.
     *
     * @param Rma $rma
     * @return array
     */
    protected function getRmaItems(Rma $rma)
    {
        $rmaItems = $rma->getItems();
        /** @var OrderInjectable $order */
        $order = $rma->getDataFieldConfig('order_id')['source']->getOrder();
        $orderItems = $order->getEntityId();

        foreach ($rmaItems as $productKey => $productData) {
            $key = str_replace('product_key_', '', $productKey);
            $product = $orderItems[$key];

            $productData['sku'] = $this->prepareProductSku($product);
            $productData['qty'] = $productData['qty_requested'];
            if (!isset($productData['status'])) {
                $productData['status'] = 'Pending';
            }
            unset($productData['reason']);
            unset($productData['reason_other']);

            $rmaItems[$productKey] = $productData;
        }

        return $rmaItems;
    }

    /**
     * Return product sku.
     *
     * @param FixtureInterface $product
     * @return string
     */
    protected function prepareProductSku(FixtureInterface $product)
    {
        return $product->getSku();
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Correct return request is present on frontend.';
    }
}
