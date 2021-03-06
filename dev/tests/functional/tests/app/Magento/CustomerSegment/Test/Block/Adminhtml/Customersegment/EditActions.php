<?php
/**
 * Config actions block
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment;

use Magento\Backend\Test\Block\FormPageActions as AbstractPageActions;

/**
 * Class EditActions
 * Edit actions block
 */
class EditActions extends AbstractPageActions
{
    /**
     * Custom "Save and Continue Edit" button
     *
     * @var string
     */
    protected $saveAndContinueButton = '#save_and_continue_edit';
}
