<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Mtf\Fixture\FixtureInterface;
use Mtf\ObjectManager;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Rma\Test\Page\CustomerAccountRmaIndex;
use Magento\Rma\Test\Page\CustomerAccountRmaView;
use Magento\Rma\Test\Fixture\Rma;
use Magento\Rma\Test\Fixture\Rma\OrderId;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Sales\Test\Fixture\OrderInjectable\CustomerId;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertRmaItemsOnFrontend
 * Assert customer can vew return request on Frontend and verify.
 */
class AssertRmaItemsOnFrontend extends AbstractAssertForm
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert customer can vew return request on Frontend (MyAccount - My Returns) and verify:
     * - product name
     * - product sku
     * - conditions
     * - resolution
     * - requested qty
     * - status
     *
     * @param ObjectManager $objectManager
     * @param Rma $rma
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerAccountRmaIndex $customerAccountRmaIndex
     * @param CustomerAccountRmaView $customerAccountRmaView
     * @return void
     */
    public function processAssert(
        ObjectManager $objectManager,
        Rma $rma,
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        CustomerAccountRmaIndex $customerAccountRmaIndex,
        CustomerAccountRmaView $customerAccountRmaView
    ) {
        /** @var OrderId $sourceOrderId */
        $sourceOrderId = $rma->getDataFieldConfig('order_id')['source'];
        $order = $sourceOrderId->getOrder();
        /** @var CustomerId $sourceCustomerId */
        $sourceCustomerId = $order->getDataFieldConfig('customer_id')['source'];
        $customer = $sourceCustomerId->getCustomer();

        $objectManager->create(
            '\Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        )->run();

        $cmsIndex->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Returns');
        $customerAccountRmaIndex->getRmaHistory()->getRmaTable()->getRmaRow($rma)->clickView();
        $pageItemsData = $this->sortDataByPath(
            $customerAccountRmaView->getRmaView()->getRmaItems()->getData(),
            '::sku'
        );
        $fixtureItemsData = $this->sortDataByPath(
            $this->getRmaItems($rma),
            '::sku'
        );

        $objectManager->create('\Magento\Customer\Test\TestStep\LogoutCustomerOnFrontendStep')->run();

        \PHPUnit_Framework_Assert::assertEquals($fixtureItemsData, $pageItemsData);
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
        /** @var OrderId $sourceOrderId */
        $sourceOrderId = $rma->getDataFieldConfig('order_id')['source'];
        $order = $sourceOrderId->getOrder();
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
     * @return mixed
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
        return 'Return request is present on frontend and verify.';
    }
}
