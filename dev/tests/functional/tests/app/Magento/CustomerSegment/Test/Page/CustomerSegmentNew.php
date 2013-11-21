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

use Magento\Backend\Test\Block\CustomerSegment\Actions;
use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\Core\Test\Block\Messages;
use Magento\CustomerSegment\Test\Block\Backend\CustomerSegmentForm;
use Magento\CustomerSegment\Test\Block\Backend\MatchedCustomerGrid;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class for creating new customer in backend page
 *
 * @package Magento\CustomerSegment\Test\Page
 */
class CustomerSegmentNew extends Page {
    /**
     * URL for new customer segment
     */
    const MCA = 'admin/customersegment/new';

    /**
     * @var CustomerSegmentForm
     */
    protected $newCustomerSegmentForm;

    /**
     * @var Messages
     */
    protected $messagesBlock;
    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->newCustomerSegmentForm = Factory::getBlockFactory()
            ->getMagentoCustomerSegmentBackendCustomerSegmentForm($this->_browser->find('[id="page:main-container"]')
        );
        $this->messagesBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('#messages .messages')
        );
    }

    /**
     * Get new customer form
     *
     * @return CustomerSegmentForm
     */
    public function getNewCustomerSegmentForm()
    {
        return $this->newCustomerSegmentForm;
    }

    /**
     * Getter for global page message
     *
     * @return Messages
     */
    public function getMessageBlock()
    {
        return $this->messagesBlock;
    }

    /**
     * Refresh global page message
     *
     */
    public function setMessageBlock()
    {
        $this->messagesBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('#messages .messages')
        );
    }

    /**
     * Get Conditions tabs block
     *
     * @return FormTabs
     */
    public function getConditionsTab()
    {
        return Factory::getBlockFactory()->getMagentoBackendWidgetFormTabs(
            $this->_browser->find('#magento_customersegment_segment_tabs_conditions_section')
        );
    }

    /**
     * Get Customers tabs block
     *
     * @return FormTabs
     */
    public function getCustomersTab()
    {
        return Factory::getBlockFactory()->getMagentoBackendWidgetFormTabs(
            $this->_browser->find('#magento_customersegment_segment_tabs_customers_tab')
        );
    }

    /**
     * Get add condition block
     *
     * @return Actions
     */
    public function getConditions()
    {
        return Factory::getBlockFactory()->getMagentoBackendCustomerSegmentActions(
            $this->_browser->find('#conditions__1__children')
        );
    }

    /**
     * Get add condition block
     *
     * @return Actions
     */
    public function getSave()
    {
        return Factory::getBlockFactory()->getMagentoBackendCustomerSegmentActions(
            $this->_browser->find('.page-actions')
        );
    }

    /**
     * Get matched customer grid block
     *
     * @return MatchedCustomerGrid
     */
    public function getCustomerGridBlock()
    {
        return Factory::getBlockFactory()->getMagentoCustomerSegmentBackendMatchedCustomerGrid(
            $this->_browser->find('#segmentGrid')
        );
    }
}