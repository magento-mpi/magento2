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
}
