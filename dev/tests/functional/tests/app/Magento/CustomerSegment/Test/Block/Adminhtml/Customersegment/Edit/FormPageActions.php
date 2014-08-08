<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\Edit;

use Magento\Backend\Test\Block\FormPageActions as ParentFormPageActions;
use Mtf\Client\Element\Locator;

/**
 * Class FormPageActions
 * Form page actions
 */
class FormPageActions extends ParentFormPageActions
{
    /**
     * Customer segment "Save and Continue Edit" button
     *
     * @var string
     */
    protected $saveAndContinueButton = '#save_and_continue_edit';

    /**
     * Customer segment "Refresh Segment Data" button
     *
     * @var string
     */
    protected $refreshSegmentData = '#match_customers';

    /**
     * Click on "Refresh Segment Data" button
     *
     * @return void
     */
    public function refreshSegmentData()
    {
        $this->_rootElement->find($this->refreshSegmentData)->click();
    }
}
