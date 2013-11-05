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

namespace Magento\Backend\Test\Block\Catalog;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Product
 * Catalog manage products block
 *
 * @package Magento\Backend\Test\Block\Catalog
 */
class Product extends Block
{
    /**
     * Product toggle button
     *
     * @var string
     */
    private $toggleButton;

    /**
     * Initialize block elements
     */
    protected function _init()
    {
        //Elements
        $this->toggleButton = '[data-ui-id=products-list-add-new-button-dropdown]';
    }

    /**
     * Add product using split button
     *
     * @param string $productType
     */
    public function addProduct($productType = 'simple')
    {
        $this->_rootElement->find($this->toggleButton, Locator::SELECTOR_CSS)->click();
        $this->_rootElement->find('[data-ui-id=products-list-add-new-button-item-' . $productType . ']',
            Locator::SELECTOR_CSS)->click();
    }
}
