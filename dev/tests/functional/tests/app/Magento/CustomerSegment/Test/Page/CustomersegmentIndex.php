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

namespace Magento\CustomerSegment\Test\Page;

use Magento\CustomerSegment\Test\Block\Backend\Adminhtml\Customersegment\Grid;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class CustomerSegment
 * CustomerSegment backend grid page.
 *
 * @package Magento\CustomerSegment\Test\Page
 */
class CustomersegmentIndex extends Page
{
    /**
     * URL for customer segment
     */
    const MCA = 'customersegment/index';

    /**
     * @var Grid
     */
    protected $customerSegmentGridBlock = '[id="page:main-container"]';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Getter for customer segment grid block
     *
     * @return Grid
     */
    public function getCustomerSegmentGridBlock()
    {
        return Factory::getBlockFactory()->getMagentoCustomerSegmentBackendAdminhtmlCustomersegmentGrid(
            $this->_browser->find($this->customerSegmentGridBlock, Locator::SELECTOR_CSS)
        );
    }
}
