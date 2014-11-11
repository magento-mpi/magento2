<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Block\Product;

use Magento\Catalog\Test\Fixture\CatalogProductAttribute;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Block\Block;

/**
 * Product additional information block on the product page.
 */
class Additional extends Block
{
    /**
     * Custom attribute selector.
     *
     * @var string
     */
    protected $attributeSelector = '//tr/th';

    /**
     * Custom attribute value selector.
     *
     * @var string
     */
    protected $attributeValueSelector = '/following::td[1]';

    /**
     * Get product attributes.
     *
     * @return Element[]
     */
    public function getProductAttributes()
    {
        $data = [];
        $elements = $this->_rootElement->find($this->attributeSelector, Locator::SELECTOR_XPATH)->getElements();
        foreach ($elements as $element) {
            $data[$element->getText()] = $this->_rootElement->find(
                $this->attributeSelector . $this->attributeValueSelector,
                Locator::SELECTOR_XPATH
            );
        }
        return $data;
    }

    /**
     * Check if attribute value contains tag.
     *
     * @param CatalogProductAttribute $attribute
     * @return bool
     */
    public function hasHtmlTagInAttributeValue(CatalogProductAttribute $attribute)
    {
        $element = $this->getProductAttributes()[$attribute->getFrontendLabel()];
        return $this->checkHtmlTagStructure($element)->isVisible();
    }

    /**
     * Find <b><i> </i></b> tag structure in element.
     *
     * @param Element $element
     * @return Element
     */
    protected function checkHtmlTagStructure(Element $element)
    {
        return $element->find('b i');
    }
}
