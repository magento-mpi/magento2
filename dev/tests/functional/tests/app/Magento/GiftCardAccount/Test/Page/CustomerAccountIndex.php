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
    // TODO: remove "index" after fix in old test generate factory
    const MCA = 'customer/account';

    protected $_blocks = [
        'messages' => [
            'name' => 'messages',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.page.messages',
            'strategy' => 'css selector',
        ],
        'accountMenuBlock' => [
            'name' => 'accountMenuBlock',
            'class' => 'Magento\Customer\Test\Block\Account\Links',
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
            'class' => 'Magento\CustomerBalance\Test\Block\Account\History',
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
     * @return \Magento\CustomerBalance\Test\Block\Account\History
     */
    public function getStoreCreditBlock()
    {
        return $this->getBlockInstance('storeCreditBlock');
    }

    /**
     * @return \Magento\Customer\Test\Block\Account\Links
     */
    public function getAccountMenuBlock()
    {
        return $this->getBlockInstance('accountMenuBlock');
    }
} 