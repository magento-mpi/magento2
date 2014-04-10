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
use Magento\Backend\Test\Block\GridPageActions;

/**
 * Class CustomerIndex
 * Customer backend grid page.
 *
 * @package Magento\Customer\Test\Page
 */
class CustomerIndex extends Page
{
    /**
     * URL for customer login
     */
    const MCA = 'customer/index';

    /**
     * Backend customer grid
     *
     * @var string
     */
    protected $gridBlock = '#customerGrid';

    /**
     * Grid page actions block
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
     * Getter for customer grid block
     *
     * @return \Magento\Customer\Test\Block\Backend\CustomerGrid
     */
    public function getGridBlock()
    {
        return Factory::getBlockFactory()->getMagentoCustomerBackendCustomerGrid(
            $this->_browser->find($this->gridBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get Grid page actions block
     *
     * @return GridPageActions
     */
    public function getActionsBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendGridPageActions(
            $this->_browser->find($this->pageActionsBlock)
        );
    }
}
