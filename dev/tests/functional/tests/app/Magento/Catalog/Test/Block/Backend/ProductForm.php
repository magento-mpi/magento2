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

/**
 * Class ProductForm
 * Product creation form
 *
 * @package Magento\Catalog\Test\Block
 */
class ProductForm extends FormTabs
{
    /**
     * Choose affected attribute set dialog popup window
     *
     * @var AffectedAttributeSet
     */
    private $affectedAttributeSetBlock;

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
                '\\Magento\\Backend\\Test\\Block\\Catalog\\Product\\Edit\\Tab\\Super\\Config'
        );
        //Elements
        $this->saveButton = '#save-split-button-button';
        //Blocks
        $this->affectedAttributeSetBlock = Factory::getBlockFactory()->
            getMagentoCatalogProductConfigurableAffectedAttributeSet(
                $this->_rootElement->find(
                    "//*[contains(@class, ui-dialog)]//*[@id='affected-attribute-set-form']/..",
                    Locator::SELECTOR_XPATH
                )
            );
    }

    /**
     * Get choose affected attribute set dialog popup window
     *
     * @return \Magento\Catalog\Test\Block\Product\Configurable\AffectedAttributeSet
     */
    protected function getAffectedAttributeSetBlock()
    {
        return $this->affectedAttributeSetBlock;
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
        $this->_rootElement->find('category_ids-suggest', Locator::SELECTOR_ID)->setValue($name);
        $parentLocation = '//*[@id="attribute-category_ids-container"]';
        $categoryListLocation = $parentLocation . '//div[@class="mage-suggest-dropdown"]';
        $this->waitForElementVisible($categoryListLocation, Locator::SELECTOR_XPATH);
        $categoryLocation = $parentLocation . '//li[contains(@data-suggest-option, \'"label":"' . $name . '",\')]//a';
        $this->_rootElement->find($categoryLocation, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Save product
     *
     * @param Fixture $fixture
     */
    public function save(Fixture $fixture)
    {
        parent::save($fixture);
        if ($this->getAffectedAttributeSetBlock()->isVisible()) {
            $this->getAffectedAttributeSetBlock()->chooseAttributeSet($fixture);
        }
    }
}
