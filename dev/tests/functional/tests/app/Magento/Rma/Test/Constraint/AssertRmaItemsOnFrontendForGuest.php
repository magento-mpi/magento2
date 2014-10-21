<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Constraint;

use Mtf\ObjectManager;
use Magento\Sales\Test\Page\SalesGuestView;
use Magento\Rma\Test\Page\RmaGuestIndex;
use Magento\Rma\Test\Page\RmaGuestView;
use Magento\Rma\Test\Fixture\Rma;
use Magento\Rma\Test\Fixture\Rma\OrderId;

/**
 * Class AssertRmaItemsOnFrontendForGuest
 * Assert guest can vew return request on Frontend (MyAccount - My Returns) and verify.
 */
class AssertRmaItemsOnFrontendForGuest extends AssertRmaItemsOnFrontend
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert guest can vew return request on Frontend (MyAccount - My Returns) and verify:
     * - product name
     * - product sku
     * - conditions
     * - resolution
     * - requested qty
     * - status
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
        /** @var OrderId $sourceOrderId */
        $sourceOrderId = $rma->getDataFieldConfig('order_id')['source'];
        $order = $sourceOrderId->getOrder();
        ObjectManager::getInstance()->create(
            '\Magento\Sales\Test\TestStep\OpenSalesViewOnFrontendForGuest',
            ['order' => $order]
        )->run();

        $salesGuestView->getViewBlock();
        // $salesGuestView->getViewBlock()->clickLink('Returns');
        $rmaGuestIndex->open();
        $rmaGuestIndex->getReturnsBlock()->getRmaTable()->getRmaRow($rma)->clickView();

        $fixtureRmaItems = $this->sortDataByPath($this->getRmaItems($rma), '::sku');
        $pageRmaItems = $this->sortDataByPath($rmaGuestView->getRmaView()->getRmaItems()->getData(), '::sku');
        \PHPUnit_Framework_Assert::assertEquals($fixtureRmaItems, $pageRmaItems);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Return request is present on frontend and verify for guest.';
    }
}
