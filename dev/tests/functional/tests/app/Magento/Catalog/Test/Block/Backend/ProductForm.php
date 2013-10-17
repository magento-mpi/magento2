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
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\FormTabs;

/**
 * Class ProductForm
 * Product creation form
 *
 * @package Magento\Catalog\Test\Block
 */
class ProductForm extends FormTabs
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Custom tab classes for product form
        $this->_tabClasses = array(
            'product_info_tabs_bundle_content' =>
            '\\Magento\\Bundle\\Test\\Block\\Adminhtml\\Catalog\\Product\\Edit\\Tab\\Bundle'
        );
        $this->saveButton = '#save-split-button-button';
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

        parent::fill($fixture);
    }

    /**
     * Select category
     */
    public function fillCategory($name)
    {
        $this->_rootElement->find('category_ids-suggest', Locator::SELECTOR_ID)->setValue($name);
        $parentLocation = '//*[@id="attribute-category_ids-container"]';
        $categoryListLocation = $parentLocation . '//div[@class="mage-suggest-dropdown"]';
        $this->waitForElementVisible($categoryListLocation, Locator::SELECTOR_XPATH);
            $categoryLocation = $parentLocation . '//li[contains(@data-suggest-option, \'"label":"' . $name . '",\')]//a';
        $this->_rootElement->find($categoryLocation, Locator::SELECTOR_XPATH)->click();
    }
}
