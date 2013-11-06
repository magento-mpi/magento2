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

use Magento\Backend\Test\Block\PageActions;
use Magento\Customer\Test\Block\Backend\CustomerGrid;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class AdminCustomer
 * Customer backend grid page.
 *
 * @package Magento\Customer\Test\Page
 */
class AdminCustomer extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'admin/customer';

    /**
     * @var CustomerGrid
     */
    protected $_customerGridBlock;

    /**
     * @var PageActions
     */
    protected $_pageActionsBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->_customerGridBlock = Factory::getBlockFactory()->getMagentoCustomerBackendCustomerGrid(
            $this->_browser->find('#customerGrid')
        );
        $this->_pageActionsBlock = Factory::getBlockFactory()->getMagentoBackendPageActions(
            $this->_browser->find('.page-actions')
        );
    }

    /**
     * Getter for customer grid block
     *
     * @return CustomerGrid
     */
    public function getCustomerGridBlock()
    {
        return $this->_customerGridBlock;
    }

    /**
     * Getter for page actions block
     *
     * @return PageActions
     */
    public function getPageActionsBlock()
    {
        return $this->_pageActionsBlock;
    }
}
