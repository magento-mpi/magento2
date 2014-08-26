<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class CustomerAccountIndex
 */
class CustomerAccountIndex extends FrontendPage
{
    const MCA = 'customer/account/index';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'messages' => [
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '.page.messages',
            'strategy' => 'css selector',
        ],
        'dashboardAddress' => [
            'class' => 'Magento\Customer\Test\Block\Account\Dashboard\Address',
            'locator' => '.block-dashboard-addresses',
            'strategy' => 'css selector',
        ],
        'titleBlock' => [
            'class' => 'Magento\Theme\Test\Block\Html\Title',
            'locator' => '.page-title',
            'strategy' => 'css selector',
        ],
        'accountMenuBlock' => [
            'class' => 'Magento\Customer\Test\Block\Account\Links',
            'locator' => '.nav.items',
            'strategy' => 'css selector',
        ],
        'infoBlock' => [
            'class' => 'Magento\Customer\Test\Block\Account\Dashboard\Info',
            'locator' => '.column.main',
            'strategy' => 'css selector',
        ],
        'compareProductsBlock' => [
            'class' => 'Magento\Catalog\Test\Block\Product\Compare\Sidebar',
            'locator' => '.block.compare',
            'strategy' => 'css selector',
        ],
        'redeemBlock' => [
            'class' => 'Magento\GiftCardAccount\Test\Block\Account\Redeem',
            'locator' => '#giftcard-form',
            'strategy' => 'css selector',
        ],
        'storeCreditBlock' => [
            'class' => 'Magento\CustomerBalance\Test\Block\Account\History',
            'locator' => '#maincontent',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessages()
    {
        return $this->getBlockInstance('messages');
    }

    /**
     * @return \Magento\Customer\Test\Block\Account\Dashboard\Address
     */
    public function getDashboardAddress()
    {
        return $this->getBlockInstance('dashboardAddress');
    }

    /**
     * @return \Magento\Theme\Test\Block\Html\Title
     */
    public function getTitleBlock()
    {
        return $this->getBlockInstance('titleBlock');
    }

    /**
     * @return \Magento\Customer\Test\Block\Account\Links
     */
    public function getAccountMenuBlock()
    {
        return $this->getBlockInstance('accountMenuBlock');
    }

    /**
     * @return \Magento\Customer\Test\Block\Account\Dashboard\Info
     */
    public function getInfoBlock()
    {
        return $this->getBlockInstance('infoBlock');
    }

    /**
     * @return \Magento\Catalog\Test\Block\Product\Compare\Sidebar
     */
    public function getCompareProductsBlock()
    {
        return $this->getBlockInstance('compareProductsBlock');
    }

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
}
