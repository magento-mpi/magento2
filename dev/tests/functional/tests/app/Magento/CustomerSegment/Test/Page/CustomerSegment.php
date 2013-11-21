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

use Magento\Backend\Test\Block\PageActions;
use Magento\CustomerSegment\Test\Block\Backend\CustomerSegmentGrid;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class CustomerSegment
 * CustomerSegment backend grid page.
 *
 * @package Magento\CustomerSegment\Test\Page
 */
class CustomerSegment extends Page {
    /**
     * URL for customer segment
     */
    const MCA = 'admin/customersegment';

    /**
     * @var CustomerSegmentGrid
     */
    protected $customerSegmentGridBlock;

    /**
     * @var PageActions
     */
    protected $pageActionsBlock;

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->customerGridBlock = Factory::getBlockFactory()->getMagentoCustomerSegmentBackendCustomerSegmentGrid(
            $this->_browser->find('#customersegmentGrid')
        );
        $this->pageActionsBlock = Factory::getBlockFactory()->getMagentoBackendPageActions(
            $this->_browser->find('.page-actions')
        );
    }

    /**
     * Getter for customer segment grid block
     *
     * @return CustomerSegmentGrid
     */
    public function getCustomerSegmentGridBlock()
    {
        return $this->customerSegmentGridBlock;
    }

    /**
     * Getter for page actions block
     *
     * @return PageActions
     */
    public function getPageActionsBlock()
    {
        return $this->pageActionsBlock;
    }
}