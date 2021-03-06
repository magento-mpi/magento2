<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Rma\Test\Constraint;

use Magento\Rma\Test\Fixture\Rma;
use Magento\Rma\Test\Page\RmaGuestIndex;
use Magento\Rma\Test\Page\RmaGuestView;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\SalesGuestView;

/**
 * Assert that rma is correct display for guest on frontend (Orders and Returns).
 */
class AssertRmaOnFrontendForGuest extends AbstractAssertRmaOnFrontend
{
    /* tags */
    const SEVERITY = 'middle';
    /* end tags */

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

        $salesGuestView->getViewBlock()->openLinkByName('Returns');
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
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Correct guest's return request is present on frontend (Orders and Returns).";
    }
}
