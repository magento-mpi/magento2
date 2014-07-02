<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product\Attribute\Set;

use Magento\Backend\Test\Block\FormPageActions as AbstractFormPageActions;

/**
 * Class Form
 * Catalog Product Attribute form
 */
class FormPageActions extends AbstractFormPageActions
{
    /**
     * "Save" button
     *
     * @var string
     */
    protected $saveButton = '.save-attribute-set';

    /**
     * "Delete" button
     *
     * @var string
     */
    protected $deleteButton = '.delete';
}
