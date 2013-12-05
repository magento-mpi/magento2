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
    const MCA = 'customersegment';

    /**
     * @var Grid
     */
    protected $customerSegmentGridBlock;

    /**
     * 'Add New' segment button
     *
     * @var string
     */
    protected $addNewSegment = "//button[@id='add']";

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->customerGridBlock = Factory::getBlockFactory()->
            getMagentoCustomerSegmentBackendAdminhtmlCustomersegmentGrid($this->_browser->find('#customersegmentGrid'));
    }

    /**
     * Getter for customer segment grid block
     *
     * @return Grid
     */
    public function getCustomerSegmentGridBlock()
    {
        return $this->customerSegmentGridBlock;
    }

    /**
     * Add new segment
     */
    public function addNewSegment()
    {
        $this->_browser->find($this->addNewSegment, Locator::SELECTOR_XPATH)->click();
    }
}
