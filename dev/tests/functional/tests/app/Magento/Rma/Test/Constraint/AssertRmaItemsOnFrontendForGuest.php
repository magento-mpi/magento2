<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Constraint;

use Magento\Rma\Test\Page\RmaGuestView;
use Magento\Rma\Test\Page\RmaGuestIndex;
use Magento\Sales\Test\Page\SalesGuestView;
use Magento\Rma\Test\Fixture\Rma;
use Magento\Sales\Test\Fixture\OrderInjectable;

/**
 * Assert guest can vew return request on Frontend and verify.
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
        /** @var OrderInjectable $order */
        $order = $rma->getDataFieldConfig('order_id')['source']->getOrder();
        $this->objectManager->create(
            '\Magento\Sales\Test\TestStep\OpenSalesViewOnFrontendForGuest',
            ['order' => $order]
        )->run();

        $salesGuestView->getViewBlock()->clickLink('Returns');
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
        return "Correct guest's request is present on frontend.";
    }
}
