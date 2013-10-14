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
     * Custom tab classes for product form
     *
     * @var array
     */
    protected $_tabClasses = array(
        'product_info_tabs_bundle_content' =>
            '\\Magento\\Bundle\\Test\\Block\\Adminhtml\\Catalog\\Product\\Edit\\Tab\\Bundle'
    );

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        $this->saveButton = '#save-split-button-button';
    }

    /**
     * {inheritdoc}
     */
    public function fill(Fixture $fixture)
    {
        /**
         * Open tab "Advanced Settings" to make all nested tabs visible and available to interact
         */
        $this->_rootElement->find('ui-accordion-product_info_tabs-advanced-header-0', Locator::SELECTOR_ID)->click();

        parent::fill($fixture);
    }
}
