<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Block\Adminhtml\Rate\Edit;

use \Magento\Backend\Test\Block\FormPageActions as FormPageActionsInterface;

/**
 * Class FormPageActions
 * Form page actions block in Tax Rate new/edit page
 */
class FormPageActions extends FormPageActionsInterface
{
    /**
     * "Save Rate" button
     *
     * @var string
     */
    protected $saveButton = '.save-rate';
}
