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

use Mtf\Fixture\FixtureInterface;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\Catalog\Test\Fixture\Category;
use Magento\Bundle\Test\Fixture\Bundle;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Catalog\Test\Fixture\GroupedProduct;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;
use Magento\Downloadable\Test\Fixture\DownloadableProduct;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Related;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Upsell;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Crosssell;

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
     * Variations tab selector
     *
     * @var string
     */
    protected $variationsTab = '[data-ui-id="product-tabs-tab-content-super-config"] .title';

    /**
     * Variations wrapper selector
     *
     * @var string
     */
    protected $variationsWrapper = '[data-ui-id="product-tabs-tab-content-super-config"]';

    /**
     * New variation set button selector
     *
     * @var string
     */
    protected $newVariationSet = '[data-ui-id="admin-product-edit-tab-super-config-grid-container-add-attribute"]';

    /**
     * Choose affected attribute set dialog popup window
     *
     * @var string
     */
    protected $affectedAttributeSet = "//div[div/@data-id='affected-attribute-set-selector']";

    /**
     * @var array
     */
    protected $tabClasses = array(
        Bundle::GROUP => '\Magento\Bundle\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle',
        ConfigurableProduct::GROUP => '\Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config',
        GroupedProduct::GROUP => '\Magento\Catalog\Test\Block\Product\Grouped\AssociatedProducts',
        DownloadableProduct::GROUP => '\Magento\Downloadable\Test\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable',
        Product::GROUP_CUSTOM_OPTIONS => '\Magento\Catalog\Test\Block\Adminhtml\Product\Edit\CustomOptionsTab',
        Related::GROUP => 'Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Related',
        Upsell::GROUP => 'Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Upsell',
        Crosssell::GROUP => 'Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Crosssell'
    );

    /**
     * @var Category
     */
    protected $category;

    /**
     * Get choose affected attribute set dialog popup window
     *
     * @return \Magento\Catalog\Test\Block\Product\Configurable\AffectedAttributeSet
     */
    protected function getAffectedAttributeSetBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogProductConfigurableAffectedAttributeSet(
            $this->_rootElement->find($this->affectedAttributeSet, Locator::SELECTOR_XPATH)
        );
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * Fill the product form
     *
     * @param FixtureInterface $fixture
     * @param Element $element
     * @return \Magento\Backend\Test\Block\Widget\FormTabs|void
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        // Open tab "Advanced Settings" to make all nested tabs visible and available to interact
        $this->showAdvanced();
        /** @var $fixture \Magento\Catalog\Test\Fixture\Product */
        $this->fillCategory($fixture);
        parent::fill($fixture);
    }

    /**
     * Select category
     *
     * @param FixtureInterface $fixture
     */
    protected function fillCategory(FixtureInterface $fixture)
    {
        // TODO should be removed after suggest widget implementation as typified element

        $categoryName = $this->category
            ? $this->category->getCategoryName()
            : ($fixture->getCategoryName() ? $fixture->getCategoryName() : '');

        if ($categoryName) {
            $this->fillCategoryField(
                $categoryName,
                'category_ids-suggest',
                '//*[@id="attribute-category_ids-container"]'
            );
        }
    }

    /**
     * Save product
     *
     * @param FixtureInterface $fixture
     * @return \Magento\Backend\Test\Block\Widget\Form|void
     */
    public function save(FixtureInterface $fixture = null)
    {
        parent::save($fixture);
        if ($this->getAffectedAttributeSetBlock()->isVisible()) {
            $this->getAffectedAttributeSetBlock()->chooseAttributeSet($fixture);
        }
    }

    /**
     * Save new category
     *
     * @param Product $fixture
     */
    public function addNewCategory(Product $fixture)
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
     * Get variations block
     *
     * @return \Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config
     */
    protected function getVariationsBlock()
    {
        return Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProductEditTabSuperConfig(
            $this->_rootElement->find($this->variationsWrapper)
        );
    }

    /**
     * Fill product variations
     *
     * @param ConfigurableProduct $variations
     */
    public function fillVariations(ConfigurableProduct $variations)
    {
        $variationsBlock = $this->getVariationsBlock();
        $variationsBlock->fillAttributeOptions($variations->getConfigurableAttributes());
        $variationsBlock->generateVariations();
        $variationsBlock->fillVariationsMatrix($variations->getVariationsMatrix());
    }

    /**
     * Open variations tab
     */
    public function openVariationsTab()
    {
        $this->_rootElement->find($this->variationsTab)->click();
    }

    /**
     * Click on 'Create New Variation Set' button
     */
    public function clickCreateNewVariationSet()
    {
        $this->_rootElement->find($this->newVariationSet)->click();
    }

    /**
     * show the Advanced block.
     */
    public function showAdvanced()
    {
        $this->_rootElement->find('ui-accordion-product_info_tabs-advanced-header-0', Locator::SELECTOR_ID)->click();
        $this->waitForElementVisible('ui-accordion-product_info_tabs-advanced-panel-0', Locator::SELECTOR_ID);
    }

    /**
     * Clear category field
     */
    public function clearCategorySelect()
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
