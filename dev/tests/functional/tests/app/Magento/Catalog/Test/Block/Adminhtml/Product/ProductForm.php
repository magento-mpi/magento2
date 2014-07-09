<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product;

use Magento\Backend\Test\Block\Widget\FormTabs;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;
use Mtf\Fixture\InjectableFixture;
use Magento\Catalog\Test\Fixture\CatalogCategory;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;

/**
 * Class ProductForm
 * Product form on backend product page
 */
class ProductForm extends FormTabs
{
    /**
     * New variation set button selector
     *
     * @var string
     */
    protected $newVariationSet = '[data-ui-id="admin-product-edit-tab-super-config-grid-container-add-attribute"]';

    /**
     * Category name selector
     *
     * @var string
     */
    protected $categoryName = '//*[contains(@class, "mage-suggest-choice")]/*[text()="%categoryName%"]';

    /**
     * 'Advanced Settings' tab
     *
     * @var string
     */
    protected $advancedSettings = '#product_info_tabs-advanced [data-role="trigger"]';

    /**
     * Advanced tab list
     *
     * @var string
     */
    protected $advancedTabList = '#product_info_tabs-advanced[role="tablist"]';

    /**
     * Advanced tab panel
     *
     * @var string
     */
    protected $advancedTabPanel = './/*[role="tablist"]//ul[!contains(@style,"overflow")]';

    /**
     * CSS locator button status of the product
     *
     * @var string
     */
    protected $onlineSwitcher = '#product-online-switcher%s + [for="product-online-switcher"]';

    /**
     * Category fixture
     *
     * @var CatalogCategory
     */
    protected $category;

    /**
     * Attribute on the Product page
     *
     * @var string
     */
    protected $attribute = './/*[contains(@class,"label")]/span[text()="%s"]';

    /**
     * Attribute Search locator the Product page
     *
     * @var string
     */
    protected $attributeSearch = '#product-attribute-search-container';

    /**
     * Selector for trigger(show/hide) of advanced setting content
     *
     * @var string
     */
    protected $advancedSettingTrigger = '#product_info_tabs-advanced [data-role="trigger"]';

    /**
     * Selector for advanced setting content
     *
     * @var string
     */
    protected $advancedSettingContent = '#product_info_tabs-advanced [data-role="content"]';

    /**
     * Fill the product form
     *
     * @param FixtureInterface $fixture
     * @param FixtureInterface|null $category
     * @param Element|null $element
     * @return $this
     */
    public function fillProduct(
        FixtureInterface $fixture,
        FixtureInterface $category = null,
        Element $element = null
    ) {
        $tabs = $this->getFieldsByTabs($fixture);
        if ($category) {
            $categoryName = ($category instanceof InjectableFixture )
                ? $category->getName()
                : $category->getCategoryName();
            $tabs['product-details']['category_ids']['value'] = $categoryName;
        }

        $this->showAdvancedSettings();
        return parent::fillTabs($tabs, $element);
    }

    /**
     * Fill the product form
     *
     * @param FixtureInterface $product
     * @param Element|null $element
     * @return FormTabs
     */
    public function fill(FixtureInterface $product, Element $element = null)
    {
        $this->showAdvancedSettings();
        return parent::fill($product, $element);
    }

    /**
     * Get data of the tabs
     *
     * @param FixtureInterface|null $fixture
     * @param Element|null $element
     * @return array
     */
    public function getData(FixtureInterface $fixture = null, Element $element = null)
    {
        $this->showAdvancedSettings();
        return parent::getData($fixture, $element);
    }

    /**
     * Show Advanced Setting
     *
     * @return void
     */
    protected function showAdvancedSettings()
    {
        if (!$this->_rootElement->find($this->advancedSettingContent)->isVisible()) {
            $this->_rootElement->find($this->advancedSettingTrigger)->click();
            $this->waitForElementVisible($this->advancedSettingContent);
        }
    }

    /**
     * Fill product variations
     *
     * @param ConfigurableProduct $variations
     * @return void
     */
    public function fillVariations(ConfigurableProduct $variations)
    {
        $variationsBlock = Factory::getBlockFactory()->getMagentoCatalogAdminhtmlProductEditTabSuperConfig(
            $this->_rootElement->find($this->variationsWrapper)
        );
        $variationsBlock->fillAttributeOptions($variations->getConfigurableAttributes());
        $variationsBlock->generateVariations();
        $variationsBlock->fillVariationsMatrix($variations->getVariationsMatrix());
    }

    /**
     * Save new category
     *
     * @param Product $fixture
     * @return void
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
     * Select parent category for new one
     *
     * @return void
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
     * Clear category field
     *
     * @return void
     */
    public function clearCategorySelect()
    {
        $selectedCategory = 'li.mage-suggest-choice span.mage-suggest-choice-close';
        if ($this->_rootElement->find($selectedCategory)->isVisible()) {
            $this->_rootElement->find($selectedCategory)->click();
        }
    }

    /**
     * Open new category dialog
     *
     * @return void
     */
    protected function openNewCategoryDialog()
    {
        $this->_rootElement->find('#add_category_button', Locator::SELECTOR_CSS)->click();
        $this->waitForElementVisible('input#new_category_name');
    }

    /**
     * Check visibility of the attribute on the product page
     *
     * @param mixed $productAttribute
     * @return bool
     */
    public function checkAttributeLabel($productAttribute)
    {
        $frontendLabel = (is_array($productAttribute))
            ? $productAttribute['frontend_label']
            : $productAttribute->getFrontendLabel();
        $attributeLabelLocator = sprintf($this->attribute, $frontendLabel);

        return $this->_rootElement->find($attributeLabelLocator, Locator::SELECTOR_XPATH)->isVisible();
    }

    /**
     * Call method that checking present attribute in search result
     *
     * @param CatalogProductAttribute $productAttribute
     * @return bool
     */
    public function checkAttributeInSearchAttributeForm($productAttribute)
    {
        return $this->_rootElement->find(
            $this->attributeSearch,
            Locator::SELECTOR_CSS,
            'Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Attributes\Search'
        )->isExistAttributeInSearchResult($productAttribute);
    }
}
