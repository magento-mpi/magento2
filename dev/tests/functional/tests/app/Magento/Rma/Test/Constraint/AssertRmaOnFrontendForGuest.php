<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Constraint;

use Magento\Rma\Test\Fixture\Rma;
use Mtf\Constraint\AbstractAssertForm;
use Magento\Rma\Test\Page\RmaGuestView;
use Magento\Rma\Test\Page\RmaGuestIndex;
use Magento\Sales\Test\Page\SalesGuestView;
use Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Assert that rma is correct display for guest on frontend (Orders and Returns).
 */
class AssertRmaOnFrontendForGuest extends AbstractAssertForm
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that rma is correct display for guest on frontend (Orders and Returns):
     * - status on rma history page
     * - details and items on rma view page
     *
     * @param Rma $rma
     * @param SalesGuestView $salesGuestView
     * @param RmaGuestIndex $rmaGuestIndex
     * @param RmaGuestView $rmaGuestView
     * @return void
     */
    public function processAssert(
        Rma $rma,
        SalesGuestView $salesGuestView,
        RmaGuestIndex $rmaGuestIndex,
        RmaGuestView $rmaGuestView
    ) {
        /** @var OrderInjectable $order */
        $order = $rma->getDataFieldConfig('order_id')['source']->getOrder();
        $this->objectManager->create(
            '\Magento\Sales\Test\TestStep\OpenSalesOrderOnFrontendForGuestStep',
            ['order' => $order]
        )->run();

        $salesGuestView->getViewBlock()->clickLink('Returns');
        $fixtureRmaStatus = $rma->getStatus();
        $pageRmaData = $rmaGuestIndex->getReturnsBlock()->getRmaRow($rma)->getData();
        \PHPUnit_Framework_Assert::assertEquals(
            $fixtureRmaStatus,
            $pageRmaData['status'],
            "\nWrong display status of rma."
            . "\nExpected: " . $fixtureRmaStatus
            . "\nActual: " . $pageRmaData['status']
        );

        $rmaGuestIndex->getReturnsBlock()->getRmaRow($rma)->clickView();
        $fixtureRmaItems = $this->sortDataByPath($this->getRmaItems($rma), '::sku');
        $pageRmaItems = $this->sortDataByPath($rmaGuestView->getRmaView()->getRmaItems()->getData(), '::sku');
        \PHPUnit_Framework_Assert::assertEquals($fixtureRmaItems, $pageRmaItems);
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

            $productData['sku'] = $product->getSku();
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
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Correct guest's request is present on frontend.";
    }
}
