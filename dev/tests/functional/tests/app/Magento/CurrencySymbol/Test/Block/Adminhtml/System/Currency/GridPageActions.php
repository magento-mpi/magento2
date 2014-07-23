<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Test\Block\Adminhtml\System\Currency;

use  Magento\Backend\Test\Block\PageActions;

class GridPageActions extends PageActions
{
    /**
     * Import button locator
     *
     * @var string
     */
    protected $importButton = '[data-ui-id="adminhtml-system-currency-import-button"]';

    /**
     * Click Import button
     *
     * @throws \Exception
     * @return void
     */
    public function clickImportButton()
    {
        if ($this->_rootElement->find($this->importButton)->isVisible()) {
            $this->_rootElement->find($this->importButton)->click();
        } else {
            throw new \Exception('Import button \'' . $this->importButton . '\' is absent');
        }
    }
}
