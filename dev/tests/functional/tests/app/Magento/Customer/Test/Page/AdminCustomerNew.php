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

use Magento\Core\Test\Block\Messages;
use Magento\Customer\Test\Block\Backend\CustomerForm;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class for creating new customer in backend page
 *
 * @package Magento\Customer\Test\Page
 */
class AdminCustomerNew extends Page
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
     * @var Messages
     */
    protected $_messagesBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->_newCustomerForm = Factory::getBlockFactory()->getMagentoCustomerBackendCustomerForm(
            $this->_browser->find('[id="page:main-container"]')
        );
        $this->_messagesBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('#messages')
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

    /**
     * Getter for global page message
     *
     * @return Messages
     */
    public function getMessageBlock()
    {
        return $this->_messagesBlock;
    }
}
