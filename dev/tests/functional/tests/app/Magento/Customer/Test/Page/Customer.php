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
 * Class Customer
 * Customer backend grid page.
 *
 * @package Magento\Customer\Test\Page
 */
class Customer extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'customer';

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
        return Factory::getBlockFactory()->getMagentoCustomerBackendCustomerGrid(
            $this->_browser->find('#customerGrid')
        );
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
