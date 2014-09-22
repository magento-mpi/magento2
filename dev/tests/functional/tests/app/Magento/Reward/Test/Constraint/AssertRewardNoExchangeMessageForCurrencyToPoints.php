<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Magento\Reward\Test\Fixture\RewardRate;
use Magento\Reward\Test\Page\RewardCustomerInfo;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertRewardNoExchangeMessageForCurrencyToPoints
 * Assert that "Each $X spent will earn X Reward points" message is not displayed on the RewardCustomerInfo page
 */
class AssertRewardNoExchangeMessageForCurrencyToPoints extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that "Each $X spent will earn X Reward points" message is not displayed on the RewardCustomerInfo page.
     *
     * @param CustomerInjectable $customer
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountIndex $customerAccountIndex
     * @param RewardCustomerInfo $rewardCustomerInfo
     * @param RewardRate $rate
     * @return void
     */
    public function processAssert(
        CustomerInjectable $customer,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout,
        CustomerAccountIndex $customerAccountIndex,
        RewardCustomerInfo $rewardCustomerInfo,
        RewardRate $rate
    ) {
        //Ensure that customer is logged out
        $customerAccountLogout->open();
        $customerAccountLogin->open();
        $customerAccountLogin->getLoginBlock()->fill($customer);
        $customerAccountLogin->getLoginBlock()->submit();

        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('Reward Points');
        $actualInformation = $rewardCustomerInfo->getRewardPointsBlock()->getRewardPointsBalance();
        $expectedMessage = sprintf(
            'Each $%s spent will earn %d Reward points.',
            $rate->getValue(),
            $rate->getEqualValue()
        );

        \PHPUnit_Framework_Assert::assertFalse(
            strpos($actualInformation, $expectedMessage),
            $expectedMessage . ' is displayed on the RewardCustomerInfo page.'
        );
    }

    /**
     * Returns string representation of assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Each $X spent will earn X Reward points message is not displayed on the RewardCustomerInfo page.';
    }
}
