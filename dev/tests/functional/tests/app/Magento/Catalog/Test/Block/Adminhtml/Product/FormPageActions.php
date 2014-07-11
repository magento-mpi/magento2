<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product;

use Mtf\Page\BackendPage;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;
use Magento\Backend\Test\Block\FormPageActions as ParentFormPageActions;

/**
 * Class FormAction
 * Form action
 */
class FormPageActions extends ParentFormPageActions
{
    /**
     * Save and create new product - 'Save & New'
     */
    const SAVE_NEW = 'new';

    /**
     * Save and create a duplicate product - 'Save & Duplicate'
     */
    const SAVE_DUPLICATE = 'duplicate';

    /**
     * Save and close the product page - 'Save & Close'
     */
    const SAVE_CLOSE = 'close';

    /**
     * CSS selector toggle "Save button"
     *
     * @var string
     */
    protected $toggleButton = '[data-ui-id="page-actions-toolbar-save-split-button-dropdown"]';

    /**
     * Save type item
     *
     * @var string
     */
    protected $saveTypeItem = '#save-split-button-%s-button';

    /**
     * "Save" button
     *
     * @var string
     */
    protected $saveButton = '#save-split-button-button';

    /**
     * Save product form with window confirmation
     *
     * @param BackendPage $page
     * @param FixtureInterface $product
     * @return void
     */
    public function saveProduct(BackendPage $page, FixtureInterface $product)
    {
        parent::save();
        /** @var \Magento\Catalog\Test\Block\Adminhtml\Product\AffectedAttributeSetForm $affectedAttributeSetForm */
        $affectedAttributeSetForm = $page->getAffectedAttributeSetForm();
        $affectedAttributeSetForm->fill($product)->confirm();
    }

    /**
     * Click save and duplicate action
     *
     * @return void
     */
    public function saveAndDuplicate()
    {
        $this->_rootElement->find($this->toggleButton, Locator::SELECTOR_CSS)->click();
        $this->_rootElement->find(sprintf($this->saveTypeItem, static::SAVE_DUPLICATE), Locator::SELECTOR_CSS)->click();
    }

    /**
     * Click save and new action
     *
     * @return void
     */
    public function saveAndNew()
    {
        $this->_rootElement->find($this->toggleButton, Locator::SELECTOR_CSS)->click();
        $this->_rootElement->find(sprintf($this->saveTypeItem, static::SAVE_NEW), Locator::SELECTOR_CSS)->click();
    }

    /**
     * Click save and new action
     *
     * @return void
     */
    public function saveAndClose()
    {
        $this->_rootElement->find($this->toggleButton, Locator::SELECTOR_CSS)->click();
        $this->_rootElement->find(sprintf($this->saveTypeItem, static::SAVE_CLOSE), Locator::SELECTOR_CSS)->click();
    }
}
