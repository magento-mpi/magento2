<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super\Config\Attribute;

use Mtf\Client\Element;
use Mtf\Client\Driver\Selenium\Element\SuggestElement;
use Magento\Catalog\Test\Fixture\CatalogProductAttribute;

/**
 * Class AttributeSelector
 * Form Attribute Search on Product page
 */
class AttributeSelector extends SuggestElement
{
    /**
     * Checking exist configurable attribute in search result
     *
     * @param CatalogProductAttribute $productAttribute
     * @return bool
     */
    public function isExistAttributeInSearchResult(CatalogProductAttribute $productAttribute)
    {
        return $this->isExistValueInSearchResult($productAttribute->getFrontendLabel());
    }
}
