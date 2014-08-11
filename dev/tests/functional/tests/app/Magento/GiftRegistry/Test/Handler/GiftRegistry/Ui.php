<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Handler\GiftRegistry;

use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Ui as AbstractUi;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\GiftRegistry\Test\Page\GiftRegistryIndex;
use Magento\GiftRegistry\Test\Page\GiftRegistryAddSelect;
use Magento\GiftRegistry\Test\Page\GiftRegistryEdit;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Class Ui
 * Ui handler for creating gift registry
 */
class Ui extends AbstractUi implements GiftRegistryInterface
{
    /**
     * Cms index page
     *
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Customer account login page
     *
     * @var CustomerAccountLogin
     */
    protected $customerAccountLogin;

    /**
     * Customer account index page
     *
     * @var CustomerAccountIndex
     */
    protected $customerAccountIndex;

    /**
     * Gift Registry index page
     *
     * @var GiftRegistryIndex
     */
    protected $giftRegistryIndex;

    /**
     * Gift Registry select type page
     *
     * @var GiftRegistryAddSelect
     */
    protected $giftRegistryAddSelect;

    /**
     * Gift Registry edit type page
     *
     * @var GiftRegistryEdit
     */
    protected $giftRegistryEdit;

    /**
     * @constructor
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogout
     * @param CustomerAccountIndex $customerAccountIndex
     * @param GiftRegistryIndex $giftRegistryIndex
     * @param GiftRegistryAddSelect $giftRegistryAddSelect
     * @param GiftRegistryEdit $giftRegistryEdit
     */
    public function __construct(
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogout,
        CustomerAccountIndex $customerAccountIndex,
        GiftRegistryIndex $giftRegistryIndex,
        GiftRegistryAddSelect $giftRegistryAddSelect,
        GiftRegistryEdit $giftRegistryEdit
    ) {
        $this->cmsIndex = $cmsIndex;
        $this->customerAccountLogin = $customerAccountLogout;
        $this->customerAccountIndex = $customerAccountIndex;
        $this->giftRegistryIndex = $giftRegistryIndex;
        $this->giftRegistryAddSelect = $giftRegistryAddSelect;
        $this->giftRegistryEdit = $giftRegistryEdit;
    }

    /**
     * Create gift registry
     *
     * @param FixtureInterface $fixture
     * @return mixed|void
     */
    public function persist(FixtureInterface $fixture = null)
    {
        $this->cmsIndex->open();
        $customer = $fixture->getDataFieldConfig('customer_id')['source']->getCustomerId();
        if ($customer !== null && !$this->cmsIndex->getLinksBlock()->isLinkVisible('Log Out')) {
            $this->cmsIndex->getLinksBlock()->openLink("Log In");
            $this->customerAccountLogin->getLoginBlock()->login($customer);
        }
        $this->cmsIndex->getLinksBlock()->openLink("My Account");
        $this->customerAccountIndex->getAccountMenuBlock()->openMenuItem("Gift Registry");
        $this->giftRegistryIndex->getActionsToolbar()->addNew();
        $this->giftRegistryAddSelect->getGiftRegistryTypeBlock()->selectGiftRegistryType($fixture->getTypeId());
        $this->giftRegistryEdit->getCustomerEditForm()->fill($fixture);
        $this->giftRegistryEdit->getActionsToolbarBlock()->save();
    }
}
