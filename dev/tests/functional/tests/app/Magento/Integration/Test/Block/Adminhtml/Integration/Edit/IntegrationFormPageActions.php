<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Block\Adminhtml\Integration\Edit;

use Magento\Backend\Test\Block\FormPageActions;

/**
 * Class IntegrationFormPageActions
 * Form page actions block in Integration new/edit page
 */
class IntegrationFormPageActions extends FormPageActions
{
    /**
     * Save button
     *
     * @var string
     */
    protected $saveButton = '[data-ui-id="integration-edit-content-save-button"]';
}
