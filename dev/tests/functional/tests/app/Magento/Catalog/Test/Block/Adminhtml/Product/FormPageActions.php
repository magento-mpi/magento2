<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product;

use Magento\Backend\Test\Block\FormPageActions as blockFormPageActions;

/**
 * Class FormPageActions
 * Form action in product page on backend
 */
class FormPageActions extends blockFormPageActions
{
    /**
     * "Save" button
     *
     * @var string
     */
    protected $saveButton = '#save-split-button-button';
} 