<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Block\Adminhtml;

use Mtf\Client\Element\Locator;
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
        $this->waitForElementNotVisible($this->loader, Locator::SELECTOR_XPATH);
        $this->waitForElementNotVisible($this->loaderOld, Locator::SELECTOR_XPATH);
    }
}
