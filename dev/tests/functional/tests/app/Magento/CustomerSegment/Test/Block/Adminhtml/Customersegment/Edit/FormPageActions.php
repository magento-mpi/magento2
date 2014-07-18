<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Block\Adminhtml\Customersegment\Edit;

use Magento\Backend\Test\Block\FormPageActions as ParentFormPageActions;

/**
 * Class FormPageActions
 * Form page actions
 */
class FormPageActions extends ParentFormPageActions
{
    /**
     * Save and Continue Edit button
     *
     * @var string
     */
    protected $saveAndContinueButton = '#save_and_continue_edit';
}
