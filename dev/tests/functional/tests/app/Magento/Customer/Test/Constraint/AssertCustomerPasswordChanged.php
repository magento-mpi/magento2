<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Mtf\Constraint\AbstractConstraint;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Fixture\FixtureFactory;

/**
 * Class AssertCustomerPasswordChanged
 * Check that login again to frontend with new password was success
 */
class AssertCustomerPasswordChanged extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'Hello, %s!';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that login again to frontend with new password was success
     *
     * @param FixtureFactory $fixtureFactory
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerInjectable $initialCustomer
     * @param CustomerInjectable $customer
     * @return void
     */
    public function processAssert(
        FixtureFactory $fixtureFactory,
        CmsIndex $cmsIndex,
        CustomerAccountIndex $customerAccountIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerInjectable $initialCustomer,
        CustomerInjectable $customer
    ) {
        $cmsIndex->open();
        if ($cmsIndex->getLinksBlock()->isVisible()) {
            $cmsIndex->getLinksBlock()->openLink('Log Out');
        }

        $customer = $fixtureFactory->createByCode(
            'customerInjectable',
            [
                'dataSet' => 'default',
                'data' => [
                    'email' => $initialCustomer->getEmail(),
                    'password' => $customer->getPassword(),
                    'password_confirmation' => $customer->getPassword(),
                ],
            ]
        );

        $cmsIndex->open();
        $cmsIndex->getLinksBlock()->openLink('Log In');
        $customerAccountLogin->getLoginBlock()->login($customer);

        $customerName = $initialCustomer->getFirstname() . " " . $initialCustomer->getLastname();
        $successMessage = sprintf(self::SUCCESS_MESSAGE, $customerName);
        $actualMessage = $customerAccountIndex->getInfoBlock()->getWelcomeText();
        \PHPUnit_Framework_Assert::assertEquals(
            $successMessage,
            $actualMessage,
            'Wrong welcome message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer password was changed.';
    }
}
