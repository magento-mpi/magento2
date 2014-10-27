<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Rma\Test\Page\CustomerAccountRmaIndex;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Rma\Test\Fixture\Rma;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Assert return request is visibled on Frontend with his current status.
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
     * @param Rma $rma
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerAccountRmaIndex $customerAccountRmaIndex
     * @return void
     */
    public function processAssert(
        Rma $rma,
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        CustomerAccountRmaIndex $customerAccountRmaIndex
    ) {
        /** @var OrderInjectable $order*/
        $order = $rma->getDataFieldConfig('order_id')['source']->getOrder();
        /** @var CustomerInjectable $customer */
        $customer = $order->getDataFieldConfig('customer_id')['source']->getCustomer();

        $this->login($customer);
        $cmsIndex->getLinksBlock()->openLink('My Account');
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('My Returns');

        $fixtureRmaStatus = $rma->getStatus();
        $pageRmaData = $customerAccountRmaIndex->getRmaHistory()->getRmaTable()->getRmaRow($rma)->getData();
        \PHPUnit_Framework_Assert::assertEquals(
            $fixtureRmaStatus,
            $pageRmaData['status'],
            "\nWrong display status of rma."
            . "\nExpected: " . $fixtureRmaStatus
            . "\nActual: " . $pageRmaData['status']
        );
    }

    /**
     * Login customer.
     *
     * @param CustomerInjectable $customer
     * @return void.
     */
    protected function login(CustomerInjectable $customer)
    {
        $this->objectManager->create(
            '\Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        )->run();
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Request with correct status is present on frontend.';
    }
}
