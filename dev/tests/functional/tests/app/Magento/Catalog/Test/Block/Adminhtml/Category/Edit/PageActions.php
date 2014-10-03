<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Category\Edit;

use Magento\Backend\Test\Block\FormPageActions;

/**
 * Class PageActions
 * Category page actions
 */
class PageActions extends FormPageActions
{
    /**
     * Locator for "OK" button in warning block
     *
     * @var string
     */
    protected $warningBlock = '.ui-widget-content .ui-dialog-buttonset button:first-child';

    /**
     * Click on "Save" button
     */
    public function save()
    {
        $this->_rootElement->find($this->saveButton)->click();
        $warningBlock = $this->browser->find($this->warningBlock);
        if ($warningBlock->isVisible()) {
            $warningBlock->click();
        }
    }
}
