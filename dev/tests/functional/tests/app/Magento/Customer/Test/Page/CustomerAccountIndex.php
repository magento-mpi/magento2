<?php
/**
 * {license_notice}
 *
 * @spi
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Page;

use Mtf\Page\Page;
use Mtf\Factory\Factory;

/**
 * Frontend Customer Dashboard page
 *
 * @package Magento\Customer\Test\Page
 */
class CustomerAccountIndex extends Page
{
    /**
     * URL for customer Dashboard
     */
    const MCA = 'customer/account/index';

    /**
     * Messages block
     *
     * @var string
     */
    protected $messagesBlock = '.page.messages';

    /**
     * Address Book block
     *
     * @var string
     */
    protected $dashboardAddressBlock = '.block.dashboard.addresses';

    /**
     * Dashboard title
     *
     * @var string
     */
    protected $titleBlock = '.page.title';

    /**
     * Account menu selector
     *
     * @var string
     */
    protected $accountMenuSelector = '.nav.items';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get Messages block
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessages()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages($this->_browser->find($this->messagesBlock));
    }

    /**
     * Get Address Book block
     *
     * @return \Magento\Customer\Test\Block\Account\Dashboard\Address
     */
    public function getDashboardAddress()
    {
        return Factory::getBlockFactory()->getMagentoCustomerAccountDashboardAddress(
            $this->_browser->find($this->dashboardAddressBlock)
        );
    }

    /**
     * Get title block
     *
     * @return \Magento\Theme\Test\Block\Html\Title
     */
    public function getTitleBlock()
    {
        return $this->titleBlock = Factory::getBlockFactory()->getMagentoThemeHtmlTitle(
            $this->_browser->find($this->titleBlock)
        );
    }

    /**
     * Get Account Menu Block
     *
     * @return \Magento\Customer\Test\Block\Account\Menu
     */
    public function getAccountMenuBlock()
    {
        return Factory::getBlockFactory()->getMagentoCustomerAccountMenu(
            $this->_browser->find($this->accountMenuSelector)
        );
    }
}
