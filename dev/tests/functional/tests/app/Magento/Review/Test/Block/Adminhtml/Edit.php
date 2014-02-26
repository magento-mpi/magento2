<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Block\Adminhtml;

use Mtf\Fixture\FixtureInterface;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Form;

/**
 * Review edit form
 *
 * @package Magento\Review\Test\Block\Adminhtml
 */
class Edit extends Form
{
    /**
     * Posted by field
     *
     * @var string
     */
    protected $customer = '#customer';

    /**
     * Rating status
     *
     * @var string
     */
    protected $status = '[name=status_id]';

    /**
     * 'Save Review' button
     *
     * @var string
     */
    protected $saveButton = '[data-ui-id$=save-button]';

    /**
     * Get data from 'Posted By' field
     *
     * @return string
     */
    public function getPostedBy()
    {
        return $this->_rootElement->find($this->customer, Locator::SELECTOR_CSS)->getText();
    }

    /**
     * Get data from Status field
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_rootElement->find($this->status, Locator::SELECTOR_CSS, 'select')->getText();
    }

    /**
     * Approve review
     */
    public function approveReview()
    {
        $this->_rootElement->find($this->status, Locator::SELECTOR_CSS, 'select')->setValue('Approved');
        $this->save();
    }
}
