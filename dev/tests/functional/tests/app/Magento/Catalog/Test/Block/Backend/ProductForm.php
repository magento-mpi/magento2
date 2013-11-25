<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Backend;

use Mtf\Fixture;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\Catalog\Test\Block\Product\Configurable\AffectedAttributeSet;
use Magento\Catalog\Test\Fixture\AbstractProduct;

/**
 * Class ProductForm
 * Product creation form
 *
 * @package Magento\Catalog\Test\Block
 */
class ProductForm extends FormTabs
{
    /**
     * 'Save' split button
     *
     * @var string
     */
    protected $saveButton = '#save-split-button-button';

    /**
     * Choose affected attribute set dialog popup window
     *
     * @var string
     */
    protected $affectedAttributeSetBlock = "//*[contains(@class, ui-dialog)]//*[@id='affected-attribute-set-form']/..";

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Custom tab classes for product form
        $this->_tabClasses = array(
            'product_info_tabs_bundle_content' =>
                '\\Magento\\Bundle\\Test\\Block\\Adminhtml\\Catalog\\Product\\Edit\\Tab\\Bundle',
            'product_info_tabs_super_config_content' =>
                '\\Magento\\Catalog\\Test\\Block\\Adminhtml\\Product\\Edit\\Tab\\Super\\Config'
        );
    }

    /**
     * Get choose affected attribute set dialog popup window
     *
     * @return \Magento\Catalog\Test\Block\Product\Configurable\AffectedAttributeSet
     */
    protected function getAffectedAttributeSetBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogProductConfigurableAffectedAttributeSet(
            $this->_rootElement->find($this->affectedAttributeSetBlock,Locator::SELECTOR_XPATH)
        );
    }

    /**
     * Fill the product form
     *
     * @param Fixture $fixture
     * @param Element $element
     */
    public function fill(Fixture $fixture, Element $element = null)
    {
        /**
         * Open tab "Advanced Settings" to make all nested tabs visible and available to interact
         */
        $this->_rootElement->find('ui-accordion-product_info_tabs-advanced-header-0', Locator::SELECTOR_ID)->click();
        /** @var $fixture \Magento\Catalog\Test\Fixture\Product */
        if ($fixture->getCategoryName()) {
            $this->fillCategory($fixture->getCategoryName());
        }
        parent::fill($fixture);
    }

    /**
     * Select category
     *
     * @param string $name
     */
    protected function fillCategory($name)
    {
        // TODO should be removed after suggest widget implementation as typified element
        $this->fillCategoryField($name, 'category_ids-suggest', '//*[@id="attribute-category_ids-container"]');
    }

    /**
     * Save product
     *
     * @param Fixture $fixture
     */
    public function save(Fixture $fixture = null)
    {
        parent::save($fixture);
        if ($this->getAffectedAttributeSetBlock()->isVisible()) {
            $this->getAffectedAttributeSetBlock()->chooseAttributeSet($fixture);
        }
    }

    /**
     * Save new category
     *
     * @param AbstractProduct $fixture
     */
    public function addNewCategory(AbstractProduct $fixture)
    {
        $this->openNewCategoryDialog();
        $this->_rootElement->find('input#new_category_name', Locator::SELECTOR_CSS)
            ->setValue($fixture->getNewCategoryName());

        $this->clearCategorySelect();
        $this->selectParentCategory();

        $this->_rootElement->find('div.ui-dialog-buttonset button.action-create')->click();
        $this->waitForElementNotVisible('div.ui-dialog-buttonset button.action-create');
    }

    /**
     * Clear parent category field
     */
    protected function clearCategorySelect()
    {
        $selectedCategory = 'li.mage-suggest-choice span.mage-suggest-choice-close';
        if ($this->_rootElement->find($selectedCategory)->isVisible()) {
            $this->_rootElement->find($selectedCategory)->click();
        }
    }

    /**
     * Select parent category for new one
     */
    protected function selectParentCategory()
    {
        // TODO should be removed after suggest widget implementation as typified element
        $this->fillCategoryField(
            'Default Category',
            'new_category_parent-suggest',
            '//*[@id="new_category_form_fieldset"]'
        );
    }

    /**
     * Fills select category field
     *
     * @param string $name
     * @param string $elementId
     * @param string $parentLocation
     */
    protected function fillCategoryField($name, $elementId, $parentLocation)
    {
        // TODO should be removed after suggest widget implementation as typified element
        $this->_rootElement->find($elementId, Locator::SELECTOR_ID)->setValue($name);
        //*[@id="attribute-category_ids-container"]  //*[@id="new_category_form_fieldset"]
        $categoryListLocation = $parentLocation . '//div[@class="mage-suggest-dropdown"]'; //
        $this->waitForElementVisible($categoryListLocation, Locator::SELECTOR_XPATH);
        $categoryLocation = $parentLocation . '//li[contains(@data-suggest-option, \'"label":"' . $name . '",\')]//a';
        $this->_rootElement->find($categoryLocation, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Open new category dialog
     */
    protected function openNewCategoryDialog()
    {
        $this->_rootElement->find('#add_category_button', Locator::SELECTOR_CSS)->click();
        $this->waitForElementVisible('input#new_category_name');
    }
}
