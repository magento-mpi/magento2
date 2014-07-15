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
            'class' => 'Magento\Customer\Test\Block\Account\Dashboard\Address',
            'locator' => '.block.addresses',
            'strategy' => 'css selector',
        ],
        'titleBlock' => [
            'name' => 'titleBlock',
            'class' => 'Magento\Theme\Test\Block\Html\Title',
            'locator' => '.page-title',
            'strategy' => 'css selector',
        ],
        'accountMenuBlock' => [
            'name' => 'accountMenuBlock',
            'class' => 'Magento\Customer\Test\Block\Account\Links',
            'locator' => '.nav.items',
            'strategy' => 'css selector',
        ],
        'infoBlock' => [
            'name' => 'infoBlock',
            'class' => 'Magento\Customer\Test\Block\Account\Dashboard\Info',
            'locator' => '.column.main',
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
     * Get Account Menu Block
     *
     * @return \Magento\Customer\Test\Block\Account\Links
     */
    public function getAccountMenuBlock()
    {
        return $this->getBlockInstance('accountMenuBlock');
    }

    /**
     * Get Account Info Block
     *
     * @return \Magento\Customer\Test\Block\Account\Dashboard\Info
     */
    public function getInfoBlock()
    {
        return $this->getBlockInstance('infoBlock');
    }
}
