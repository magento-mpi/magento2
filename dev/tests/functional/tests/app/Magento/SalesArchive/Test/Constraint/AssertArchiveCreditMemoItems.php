<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\SalesArchive\Test\Constraint;

use Magento\Sales\Test\Page\Adminhtml\SalesCreditMemoView;
use Magento\SalesArchive\Test\Page\Adminhtml\ArchiveCreditMemos;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Sales\Test\Constraint\AbstractAssertItems;

/**
 * Class AssertArchiveCreditMemoItems
 * Check that returned product represented on Credit memo page
 */
class AssertArchiveCreditMemoItems extends AbstractAssertItems
{
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
            $itemsData = $this->preparePageItems($creditMemoView->getItemsBlock()->getData());
            $error = $this->verifyData($productsData, $itemsData);
            \PHPUnit_Framework_Assert::assertEmpty($error, $error);
        }
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
