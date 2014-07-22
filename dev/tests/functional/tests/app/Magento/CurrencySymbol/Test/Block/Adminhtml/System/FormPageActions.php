<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Test\Block\Adminhtml\System;

use Magento\Backend\Test\Block\FormPageActions as ParentFormPageActions;

class FormPageActions extends ParentFormPageActions
{
    /**
     * "Save" button
     *
     * @var string
     */
    protected $saveButton = '[data-ui-id="page-actions-toolbar-save-button"]';
}
