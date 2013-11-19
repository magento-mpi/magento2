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

use Magento\Core\Test\Block\Messages;
use Magento\CustomerSegment\Test\Block\Backend\CustomerSegmentForm;
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
    protected $_newCustomerSegmentForm;

    /**
     * @var Messages
     */
    protected $_messagesBlock;
    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->_newCustomerSegmentForm = Factory::getBlockFactory()
            ->getMagentoCustomerSegmentBackendCustomerSegmentForm($this->_browser->find('[id="page:main-container"]')
        );
        $this->_messagesBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
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
        return $this->_newCustomerSegmentForm;
    }

    /**
     * Getter for global page message
     *
     * @return Messages
     */
    public function getMessageBlock()
    {
        return $this->_messagesBlock;
    }
}