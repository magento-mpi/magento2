<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CatalogRule\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\FormPageActions as PageActions;

/**
 * Class FormPageActions
 * Form page actions block
 */
class FormPageActions extends PageActions
{
    /**
     * "Save and Apply" button
     *
     * @var string
     */
    protected $saveAndApplyButton = '#save_apply';

    /**
     * Click on "Save and Apply" button
     *
     * @return void
     */
    public function saveAndApply()
    {
        $this->_rootElement->find($this->saveAndApplyButton)->click();
    }
}
