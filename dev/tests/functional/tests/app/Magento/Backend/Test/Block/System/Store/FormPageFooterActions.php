<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Test\Block\System\Store;

use Magento\Backend\Test\Block\FormPageActions;

/**
 * Class FormPageFooterActions
 * Form page actions footer block
 */
class FormPageFooterActions extends FormPageActions
{
    /**
     * Click on "Delete" button without acceptAlert
     *
     * @return void
     */
    public function delete()
    {
        $this->_rootElement->find($this->deleteButton)->click();
    }
}
