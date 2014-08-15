<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\GiftRegistry\Test\Fixture\GiftRegistryType;
use Magento\GiftRegistry\Test\Page\GiftRegistryAddSelect;
use Magento\GiftRegistry\Test\Page\GiftRegistryIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertGiftRegistryTypeNotOnFrontend
 * Assert that deleted Gift Registry type is absent on creation new gift registry form on my account on frontend
 */
class AssertGiftRegistryTypeNotOnFrontend extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that deleted Gift Registry type is absent on creation new gift registry form on my account on frontend
     *
     * @param CustomerInjectable $customer
     * @param GiftRegistryType $giftRegistryType
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountIndex $customerAccountIndex
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistryAddSelect $giftRegistryAddSelect
     * @return void
     */
    public function processAssert(
        CustomerInjectable $customer,
        GiftRegistryType $giftRegistryType,
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountIndex $customerAccountIndex,
        GiftRegistryIndex $giftRegistryIndex,
        GiftRegistryAddSelect $giftRegistryAddSelect
    ) {
        $cmsIndex->open();
        if ($cmsIndex->getLinksBlock()->isLinkVisible('Log In')) {
            $cmsIndex->getLinksBlock()->openLink('Log In');
            $customerAccountLogin->getLoginBlock()->fill($customer);
            $customerAccountLogin->getLoginBlock()->submit();
        } else {
            $customerAccountIndex->open();
        }
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('Gift Registry');
        $giftRegistryIndex->getActionsToolbar()->addNew();

        \PHPUnit_Framework_Assert::assertFalse(
            $giftRegistryAddSelect->getGiftRegistryTypeBlock()->isGiftRegistryVisible($giftRegistryType->getLabel()),
            'Gift registry \'' . $giftRegistryType->getLabel() . '\' is present in dropdown.'
        );

    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift Registry type was not found at Customer Account > Gift Registry.';
    }
}
