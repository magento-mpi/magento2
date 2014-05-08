<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Block\Adminhtml\Role;

use Magento\Backend\Test\Block\FormPageActions;

/**
 * Class PageActions
 * PageActions for the role edit page
 *
 * @package Magento\User\Test\Block\Adminhtml\Role
 */
class PageActions extends FormPageActions
{
    /**
     * "Save Role" button
     *
     * @var string
     */
    protected $saveButton = '.save-role';
}
