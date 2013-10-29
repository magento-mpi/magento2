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

use Magento\Customer\Test\Block\Backend\CustomerGrid;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Customer backend grid page.
 *
 * @package Magento\Customer\Test\Page
 */
class AdminCustomerView extends Page
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
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->_customerGridBlock = Factory::getBlockFactory()->getMagentoCustomerBackendCustomerGrid(
            $this->_browser->find('#customerGrid')
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
}
