<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Test\Block\Adminhtml\System\Currency;

use  Magento\Backend\Test\Block\PageActions;

class MainPageActions extends PageActions
{
    /**
     * Save Current Rate button locator
     *
     * @var string
     */
    protected $saveCurrentRate = '[data-ui-id="page-actions-toolbar-save-button"]';

    /**
     * Save Current Rate
     *
     * @return void
     */
    public function saveCurrentRate()
    {
        $this->_rootElement->find($this->saveCurrentRate)->click();
    }
}
