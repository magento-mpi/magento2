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
use Mtf\Client\Element\Locator;
use Magento\Core\Test\Block\Messages;
use Magento\Customer\Test\Block\DashboardHeaderPanelTitle;
use Magento\Customer\Test\Block\Account\Dashboard\Address;

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
     * @var Messages
     */
    protected $messages;

    /**
     * Address Book block
     *
     * @var Address
     */
    protected $dashboardAddress;

    /**
     * Dashboard header panel title
     *
     * @var DashboardHeaderPanelTitle
     */
    protected $dashboardHeaderPanelTitle;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_frontend_url'] . self::MCA;

        $this->messages = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('//ul[@class="messages"]', Locator::SELECTOR_XPATH)
        );
        $this->dashboardAddress = Factory::getBlockFactory()->getMagentoCustomerAccountDashboardAddress(
            $this->_browser->find('.block.dashboard.addresses', Locator::SELECTOR_CSS)
        );
        $this->dashboardHeaderPanelTitle = Factory::getBlockFactory()->getMagentoCustomerDashboardHeaderPanelTitle(
            $this->_browser->find('//*[@data-ui-id="page-title" and contains(text(), "My Dashboard")]',
                Locator::SELECTOR_XPATH
            )
        );
    }

    /**
     * Get Messages block
     *
     * @return Messages
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Get Address Book block
     *
     * @return Address
     */
    public function getDashboardAddress()
    {
        return $this->dashboardAddress;
    }

    /**
     * Get dashboard panel title
     *
     * @return DashboardHeaderPanelTitle
     */
    public function getDashboardHeaderPanelTitle()
    {
        return $this->dashboardHeaderPanelTitle;
    }
}
