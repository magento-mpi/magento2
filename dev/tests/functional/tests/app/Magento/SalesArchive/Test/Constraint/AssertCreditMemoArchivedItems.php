<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\SalesArchive\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\SalesCreditMemoView;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveCreditMemos;
use Mtf\Constraint\AbstractAssertForm;

/**
 * Class AssertCreditMemoArchivedItems
 * Check that returned product represented on Credit memo page
 *
 */
class AssertCreditMemoArchivedItems extends AbstractAssertForm
{
    /**
     * Key for sort data
     *
     * @var string
     */
    protected $sortKey = "::sku";

    /**
     * List compare fields
     *
     * @var array
     */
    protected $compareFields = [
        'product',
        'sku',
        'qty'
    ];

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'medium';

    /**
     * Assert returned product represented on Credit memo page:
     * - product name
     * - qty
     *
     * @param ArchiveCreditMemos $creditMemos
     * @param SalesCreditMemoView $creditMemoView
     * @param OrderInjectable $order
     * @param array $ids
     * @return void
     */
    public function processAssert(
        ArchiveCreditMemos $creditMemos,
        SalesCreditMemoView $creditMemoView,
        OrderInjectable $order,
        array $ids
    ) {
        $productsData = $this->prepareOrderProducts($order);

        foreach ($ids['creditMemoIds'] as $creditMemoIds) {
            $filter = ['creditmemo_id' => $creditMemoIds];
            $creditMemos->open();
            $creditMemos->getCreditMemosGrid()->searchAndOpen($filter);

            $itemsData = $this->prepareCreditMemoItem($creditMemoView->getSalesCreditMemoItems()->getData());
            $error = $this->verifyData($productsData, $itemsData);
            \PHPUnit_Framework_Assert::assertEmpty($error, $error);
        }
    }

    /**
     * Prepare order products
     *
     * @param OrderInjectable $order
     * @return array
     */
    protected function prepareOrderProducts(OrderInjectable $order)
    {
        $products = $order->getEntityId()['products'];
        $productsData = [];

        /** @var CatalogProductSimple $product */
        foreach ($products as $product) {
            $productsData[] = [
                'product' => $product->getName(),
                'sku' => $product->getSku(),
                'qty' => $product->getCheckoutData()['options']['qty']
            ];
        }

        return $this->sortDataByPath($productsData, $this->sortKey);
    }

    /**
     * Prepare Credit Memo data
     *
     * @param array $itemsData
     * @return array
     */
    protected function prepareCreditMemoItem(array $itemsData)
    {
        foreach ($itemsData as $key => $itemData) {
            $itemsData[$key] = array_intersect_key($itemData, array_flip($this->compareFields));
        }
        return $this->sortDataByPath($itemsData, $this->sortKey);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product represented on Credit memo page';
    }
}
