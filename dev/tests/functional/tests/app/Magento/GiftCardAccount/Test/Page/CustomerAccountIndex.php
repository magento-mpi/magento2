<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Page;

use Magento\Customer\Test\Page\CustomerAccountIndex as AbstractCustomerAccountIndex;

/**
 * Class CustomerAccountIndex
 * Page of customer account
 */
class CustomerAccountIndex extends AbstractCustomerAccountIndex
{
    protected $_blocks = [
        'messages' => [
            'name' => 'messages',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.page.messages',
            'strategy' => 'css selector',
        ],
        'dashboardAddress' => [
            'name' => 'dashboardAddress',
            'class' => 'Magento\Customer\Block\Account\Dashboard\Address',
            'locator' => '.block.dashboard.addresses',
            'strategy' => 'css selector',
        ],
        'titleBlock' => [
            'name' => 'titleBlock',
            'class' => 'Magento\Theme\Block\Html\Title',
            'locator' => '.page.title',
            'strategy' => 'css selector',
        ],
        'accountMenuBlock' => [
            'name' => 'accountMenuBlock',
            'class' => 'Magento\GiftCardAccount\Test\Block\Account\Links',
            'locator' => '.nav.items',
            'strategy' => 'css selector',
        ],
        'redeemBlock' => [
            'name' => 'redeemBlock',
            'class' => 'Magento\GiftCardAccount\Test\Block\Account\Redeem',
            'locator' => '#giftcard-form',
            'strategy' => 'css selector',
        ],
        'storeCreditBlock' => [
            'name' => 'storeCredit',
            'class' => 'Magento\CustomerBalance\Test\Block\Account\StoreCredit',
            'locator' => '#maincontent',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\GiftCardAccount\Test\Block\Account\Redeem
     */
    public function getRedeemBlock()
    {
        return $this->getBlockInstance('redeemBlock');
    }

    /**
     * @return \Magento\CustomerBalance\Test\Block\Account\StoreCredit
     */
    public function getStoreCreditBlock()
    {
        return $this->getBlockInstance('storeCreditBlock');
    }

    /**
     * @return \Magento\GiftCardAccount\Test\Block\Account\Links
     */
    public function getAccountMenuBlock()
    {
        return $this->getBlockInstance('accountMenuBlock');
    }
} 