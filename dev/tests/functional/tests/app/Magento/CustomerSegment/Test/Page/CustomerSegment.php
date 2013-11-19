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
    protected $_customerSegmentGridBlock;

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
        $this->_customerGridBlock = Factory::getBlockFactory()->getMagentoCustomerSegmentBackendCustomerSegmentGrid(
            $this->_browser->find('#customersegmentGrid')
        );
        $this->_pageActionsBlock = Factory::getBlockFactory()->getMagentoBackendPageActions(
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
        return $this->_customerSegmentGridBlock;
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