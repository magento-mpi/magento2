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

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;
use Magento\Backend\Test\Block\FormPageActions;

/**
 * Class CustomerNew
 * New customer page in backend
 *
 * @package Magento\Customer\Test\Page
 */
class CustomerNew extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'customer/new';

    /**
     * Form for creation of the customer
     *
     * @var string
     */
    protected $customerForm = '[id="page:main-container"]';

    /**
     * Global messages block
     *
     * @var string
     */
    protected $messagesBlock = '#messages .messages';

    /**
     * Form page actions block
     *
     * @var string
     */
    protected $pageActionsBlock = '.page-main-actions';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get new customer form
     *
     * @return \Magento\Customer\Test\Block\Backend\CustomerForm
     */
    public function getNewCustomerForm()
    {
        return Factory::getBlockFactory()->getMagentoCustomerBackendCustomerForm(
            $this->_browser->find($this->customerForm, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Getter for global page message
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messagesBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get actions block on customer form
     *
     * @return FormPageActions
     */
    public function getPageActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendFormPageActions(
            $this->_browser->find($this->pageActionsBlock));
    }
}
