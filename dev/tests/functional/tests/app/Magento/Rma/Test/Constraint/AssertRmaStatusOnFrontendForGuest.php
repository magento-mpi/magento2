<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Constraint;

use Mtf\ObjectManager;
use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\SalesGuestView;
use Magento\Rma\Test\Page\RmaGuestIndex;
use Magento\Rma\Test\Fixture\Rma;
use Magento\Rma\Test\Fixture\Rma\OrderId;

/**
 * Class AssertRmaStatusOnFrontendForGuest
 * Assert that guest can check return on frontend.
 */
class AssertRmaStatusOnFrontendForGuest extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that guest can check return on frontend - Orders and Returns.
     *
     * @param Rma $rma
     * @param ObjectManager $objectManager
     * @param SalesGuestView $salesGuestView
     * @param RmaGuestIndex $rmaGuestIndex
     * @return void
     */
    public function processAssert(
        Rma $rma,
        ObjectManager $objectManager,
        SalesGuestView $salesGuestView,
        RmaGuestIndex $rmaGuestIndex
    ) {
        /** @var OrderId $sourceOrderId */
        $sourceOrderId = $rma->getDataFieldConfig('order_id')['source'];
        $order = $sourceOrderId->getOrder();
        $objectManager->create(
            '\Magento\Sales\Test\TestStep\OpenSalesViewOnFrontendForGuest',
            ['order' => $order]
        )->run();

        $salesGuestView->getViewBlock();
        // $salesGuestView->getViewBlock()->clickLink('Returns');
        $rmaGuestIndex->open();

        $fixtureRmaStatus = $rma->getStatus();
        $pageRmaData = $rmaGuestIndex->getReturnsBlock()->getRmaTable()->getRmaRow($rma)->getData();
        \PHPUnit_Framework_Assert::assertEquals(
            $fixtureRmaStatus,
            $pageRmaData['status'],
            "\nWrong display status of rma."
            . "\nExpected: " . $fixtureRmaStatus
            . "\nActual: " . $pageRmaData['status']
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Guest can check return on frontend.';
    }
}
