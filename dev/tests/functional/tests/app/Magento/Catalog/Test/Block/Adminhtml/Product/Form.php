<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Adminhtml\Product;

use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Fixture\Product;
use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\Catalog\Test\Fixture\ConfigurableProduct;
use Magento\Catalog\Test\Fixture\CatalogCategoryEntity;
use Mtf\Fixture\InjectableFixture;

/**
 * Class ProductForm
 * Product form on backend product page
 */
class Form extends FormTabs
{
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
    protected $advancedSettings = '#ui-accordion-product_info_tabs-advanced-header-0[aria-selected="false"]';

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
    protected $advancedTabPanel = '[role="tablist"] [role="tabpanel"][aria-expanded="true"]:not("overflow")';

    /**
     * Locator status of products
     *
     * @var string
     */
    protected $onlineSwitcher = '#product-online-switcher + [for="product-online-switcher"]';

    /**
     * Category fixture
     *
     * @var CatalogCategoryEntity
     */
    protected $category;

    /**
     * Fill the product form
     *
     * @param FixtureInterface $fixture
     * @param CatalogCategoryEntity $category
     * @param Element $element
     * @return $this
     */
    public function fillProduct(FixtureInterface $fixture, CatalogCategoryEntity $category = null, Element $element = null)
    {
        $this->category = $category;
        $this->fillCategory($fixture);
        if ($fixture instanceof InjectableFixture && $fixture->getStatus() === 'Product offline') {
            $this->_rootElement->find($this->onlineSwitcher)->click();
        }
        return parent::fill($fixture, $element);
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
     * Select category
     *
     * @param FixtureInterface $fixture
     * @return void|null
     */
    protected function fillCategory(FixtureInterface $fixture)
    {
        // TODO should be removed after suggest widget implementation as typified element
        $categoryName = null;
        if (!empty($this->category)) {
            $categoryName = $this->category->getName();
        }
        if (empty($categoryName) && !($fixture instanceof InjectableFixture)) {
            $categoryName = $fixture->getCategoryName();
        }
        if (empty($categoryName)) {
            return;
        }

        $category = $this->_rootElement->find(
            str_replace('%categoryName%', $categoryName, $this->categoryName), Locator::SELECTOR_XPATH
        );
        if (!$category->isVisible()) {
            $this->fillCategoryField(
                $categoryName, 'category_ids-suggest', '//*[@id="attribute-category_ids-container"]'
            );
        }
    }

    /**
     * Fills select category field
     *
     * @param string $name
     * @param string $elementId
     * @param string $parentLocation
     * @return void
     */
    protected function fillCategoryField($name, $elementId, $parentLocation)
    {
        // TODO should be removed after suggest widget implementation as typified element
        $this->_rootElement->find($elementId, Locator::SELECTOR_ID)->setValue($name);
        $this->waitForElementVisible(
            $parentLocation . '//div[@class="mage-suggest-dropdown"]',
            Locator::SELECTOR_XPATH
        );
        $this->_rootElement->find(
            $parentLocation . '//li[contains(@data-suggest-option, \'"label":"' . $name . '",\')]//a',
            Locator::SELECTOR_XPATH
        )->click();
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
     * Open tab
     *
     * @param string $tabName
     * @return Tab|bool
     */
    public function openTab($tabName)
    {
        $rootElement = $this->_rootElement;
        $selector = $this->tabs[$tabName]['selector'];
        $strategy = isset($this->tabs[$tabName]['strategy'])
            ? $this->tabs[$tabName]['strategy']
            : Locator::SELECTOR_CSS;
        $advancedTabList = $this->advancedTabList;
        $tab = $this->_rootElement->find($selector, $strategy);
        $advancedSettings = $this->_rootElement->find($this->advancedSettings);

        // Wait until all tabs will load
        $this->_rootElement->waitUntil(
            function () use ($rootElement, $advancedTabList) {
                return $rootElement->find($advancedTabList)->isVisible();
            }
        );

        if ($tab->isVisible()) {
            $tab->click();
        } elseif ($advancedSettings->isVisible()) {
            $advancedSettings->click();
            // Wait for open tab animation
            $tabPanel = $this->advancedTabPanel;
            $this->_rootElement->waitUntil(
                function () use ($rootElement, $tabPanel) {
                    return $rootElement->find($tabPanel)->isVisible();
                }
            );
            // Wait until needed tab will appear
            $this->_rootElement->waitUntil(
                function () use ($rootElement, $selector, $strategy) {
                    return $rootElement->find($selector, $strategy)->isVisible();
                }
            );
            $tab->click();
        } else {
            return false;
        }

        return $this;
    }
}
