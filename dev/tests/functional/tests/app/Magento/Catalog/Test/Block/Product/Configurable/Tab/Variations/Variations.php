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

namespace Magento\Catalog\Test\Block\Product\Configurable\Tab\Variations;

use Magento\Backend\Test\Block\Template;
use Mtf\Client\Element;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;

/**
 * Class Variations
 * Configurable variations
 *
 * @package Magento\Catalog\Test\Block\Product\Configurable\Tab\Variations
 */
class Variations extends Template
{
    /**
     * Select attribute for variations
     *
     * @param string $attributeName
     */
    public function selectAttribute($attributeName)
    {
        $attributeText = $this->_rootElement->find('#configurable-attribute-selector');
        $attributeText->setValue($attributeName);
        $attributeText->click();
        $attributeClass = '.ui-menu-item a';
        $this->waitForElementVisible($attributeClass);
        $this->_rootElement->find($attributeClass)->click();
    }

    public function generateVariations()
    {
        $this->_rootElement->find('div[data-role="product-variations-generator"] > button')->click();
    }

}
