<?php
/**
 * Config actions block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Block\Backend;

use Magento\Backend\Test\Block\FormPageActions;

/**
 * Class UserEditPageActions
 * Form page actions block
 *
 */
class UserEditPageActions extends FormPageActions
{
    /**
     * "Delete" button
     *
     * @var string
     */
    protected $deleteButton = '#delete';

    /**
     * Click on "Delete" button
     */
    public function delete()
    {
        $this->_rootElement->find($this->deleteButton)->click();
    }
}
