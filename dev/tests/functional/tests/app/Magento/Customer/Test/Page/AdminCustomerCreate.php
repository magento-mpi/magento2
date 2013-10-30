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

use Magento\Customer\Test\Block\Backend\CustomerForm;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class for creating new customer in backend page
 *
 * @package Magento\Customer\Test\Page
 */
class AdminCustomerCreate extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'admin/customer/new';

    /**
     * @var CustomerForm
     */
    protected $_newCustomerForm;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->_newCustomerForm = Factory::getBlockFactory()->getMagentoCustomerBackendCustomerForm(
            $this->_browser->find('[id="page:main-container"]')
        );
    }

    /**
     * Check for success message
     *
     * @return bool
     */
    public function waitForCustomerSaveSuccess()
    {
        $browser = $this->_browser;
        $selector = '//span[@data-ui-id="messages-message-success"]';
        $strategy = Locator::SELECTOR_XPATH;
        return $this->_browser->waitUntil(
            function () use ($browser, $selector, $strategy) {
                $customerSavedMessage = $browser->find($selector, $strategy);
                return $customerSavedMessage->isVisible() ? true : null;
            }
        );
    }

    /**
     * Get new customer form
     *
     * @return CustomerForm
     */
    public function getNewCustomerForm()
    {
        return $this->_newCustomerForm;
    }
}
