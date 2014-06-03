<?php
/**
 * Config actions block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Block\Backend\Adminhtml\Customersegment;

use Magento\Backend\Test\Block\FormPageActions as AbstractPageActions;

/**
 * Class EditActions
 *
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
