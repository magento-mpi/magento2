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
use Magento\Reward\Test\Fixture\RewardRate;
use Magento\Reward\Test\Page\RewardCustomerInfo;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertRewardNoExchangeMessageForPointsToCurrency
 * Assert that "Each X Reward points can be redeemed for $X." message is not displayed on the RewardCustomerInfo page
 */
class AssertRewardNoExchangeMessageForPointsToCurrency extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that Each X Reward points can be redeemed for $X message is not displayed on the RewardCustomerInfo page.
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
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountIndex $customerAccountIndex,
        RewardCustomerInfo $rewardCustomerInfo,
        RewardRate $rate
    ) {
        $cmsIndex->open();
        if ($cmsIndex->getLinksBlock()->isLinkVisible('Log Out')) {
            $cmsIndex->getLinksBlock()->openLink('Log Out');
        }

        $cmsIndex->getLinksBlock()->openLink('Log In');
        $customerAccountLogin->getLoginBlock()->fill($customer);
        $customerAccountLogin->getLoginBlock()->submit();

        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('Reward Points');
        $actualInformation = $rewardCustomerInfo->getRewardPointsBlock()->getRewardPointsBalance();

        $expectedMessage = sprintf(
            'Each %d Reward points can be redeemed for $%s.',
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
        return 'Each X Reward points can be redeemed for $X message is not displayed on the RewardCustomerInfo page.';
    }
}
