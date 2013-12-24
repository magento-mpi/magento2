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


use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\Core\Test\Block\Messages;
use Magento\CustomerSegment\Test\Block\Backend\Adminhtml\Report\Customer\Segment\Detail\Grid;
use Magento\CustomerSegment\Test\Block\Backend\Adminhtml\Customersegment;
use Magento\CustomerSegment\Test\Block\Backend\Adminhtml\Customersegment\Edit;
use Magento\CustomerSegment\Test\Block\Backend\Adminhtml\Customersegment\Edit\Tab\Conditions;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class for creating new customer in backend page
 *
 * @package Magento\CustomerSegment\Test\Page
 */
class CustomerSegmentNew extends Page
{
    /**
     * URL for new customer segment
     */
    const MCA = 'customersegment/index/new';

    /**
     * Form for creation of the segment
     *
     * @var string
     */
    protected $segmentForm = '[id="page:main-container"]';

    /**
     * Global messages block
     *
     * @var string
     */
    protected $messagesBlock = '#messages .messages';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get new customer form
     *
     * @return Edit
     */
    public function getNewCustomerSegmentForm()
    {
        return Factory::getBlockFactory()->getMagentoCustomerSegmentBackendAdminhtmlCustomersegmentEdit(
            $this->_browser->find($this->segmentForm, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Getter for global page message
     *
     * @return Messages
     */
    public function getMessageBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messagesBlock, Locator::SELECTOR_CSS)
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
     * @return Conditions
     */
    public function getConditions()
    {
        return Factory::getBlockFactory()->getMagentoCustomerSegmentBackendAdminhtmlCustomersegmentEditTabConditions(
            $this->_browser->find('#conditions__1__children')
        );
    }

    /**
     * Get save block
     *
     * @return Customersegment
     */
    public function getSave()
    {
        return Factory::getBlockFactory()->getMagentoCustomerSegmentBackendAdminhtmlCustomersegment(
            $this->_browser->find('.page-actions')
        );
    }

    /**
     * Get matched customer grid block
     *
     * @return Grid
     */
    public function getCustomerGridBlock()
    {
        return Factory::getBlockFactory()->getMagentoCustomerSegmentBackendAdminhtmlReportCustomerSegmentDetailGrid(
            $this->_browser->find('#segmentGrid')
        );
    }
}
