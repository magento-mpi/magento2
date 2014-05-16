<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Page;

use Mtf\Page\FrontendPage;

/**
 * Class CustomerAccountIndex
 * Page of customer account
 */
class CustomerAccountIndex extends FrontendPage
{
    const MCA = 'customer/account/index';

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
            'class' => 'Magento\Customer\Test\Block\Account\Links',
            'locator' => '.nav.items',
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
     * @return \Magento\Customer\Block\Account\Dashboard\Address
     */
    public function getDashboardAddress()
    {
        return $this->getBlockInstance('dashboardAddress');
    }

    /**
     * @return \Magento\Theme\Block\Html\Title
     */
    public function getTitleBlock()
    {
        return $this->getBlockInstance('titleBlock');
    }

    /**
     * Get Account Menu Block
     *
     * @return \Magento\Customer\Test\Block\Account\Menu
     */
    public function getAccountMenuBlock()
    {
        return $this->getBlockInstance('accountMenuBlock');
    }
}
