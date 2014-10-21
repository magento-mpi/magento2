<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Constraint;

use Magento\Sales\Test\Fixture\OrderInjectable\CustomerId;
use Mtf\Constraint\AbstractConstraint;
use Mtf\ObjectManager;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Rma\Test\Page\CustomerAccountRmaIndex;
use Magento\Rma\Test\Fixture\Rma;
use Magento\Rma\Test\Fixture\Rma\OrderId;

/**
 * Class AssertRmaStatusOnFrontend
 */
class AssertRmaStatusOnFrontend extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert return request is visibled on Frontend (MyAccount - My Returns)
     * with his current status (specified in dataset).
     *
     * @param ObjectManager $objectManager
     * @param Rma $rma
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerAccountRmaIndex $customerAccountRmaIndex
     * @return void
     */
    public function processAssert(
        ObjectManager $objectManager,
        Rma $rma,
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        CustomerAccountRmaIndex $customerAccountRmaIndex
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

        $fixtureRmaStatus = $rma->getStatus();
        $pageRmaData = $customerAccountRmaIndex->getRmaHistory()->getRmaTable()->getRmaRow($rma)->getData();

        $objectManager->create('\Magento\Customer\Test\TestStep\LogoutCustomerOnFrontendStep')->run();

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
        return 'Return request is present on Frontend with his current status.';
    }
}
