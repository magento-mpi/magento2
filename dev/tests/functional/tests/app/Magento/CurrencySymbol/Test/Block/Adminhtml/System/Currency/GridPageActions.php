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
 * Class GridPageActions
 * Grid page actions on the SystemCurrencyIndex page
 */
class GridPageActions extends PageActions
{
    /**
     * Import button locator
     *
     * @var string
     */
    protected $importButton = '[data-ui-id$="import-button"]';

    /**
     * Click Import button
     *
     * @return void
     */
    public function clickImportButton()
    {
        $this->_rootElement->find($this->importButton)->click();
    }
}
