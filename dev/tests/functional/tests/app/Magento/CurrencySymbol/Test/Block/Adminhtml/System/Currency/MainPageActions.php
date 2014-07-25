<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Test\Block\Adminhtml\System\Currency;

use Magento\Backend\Test\Block\PageActions;

/**
 * Class MainPageActions
 * Main page actions on the SystemCurrencyIndex page
 */
class MainPageActions extends PageActions
{
    /**
     * "Save Currency Rates" button locator
     *
     * @var string
     */
    protected $saveCurrentRate = '[data-ui-id="page-actions-toolbar-save-button"]';

    /**
     * Save Currency Rates
     *
     * @return void
     */
    public function saveCurrentRate()
    {
        $this->_rootElement->find($this->saveCurrentRate)->click();
    }
}
