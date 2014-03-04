<?php
/**
 * Product stock qty block for grouped product type
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Block\Stockqty\Type;

class Grouped extends \Magento\CatalogInventory\Block\Stockqty\Composite
    implements \Magento\View\Block\IdentityInterface
{
    /**
     * Retrieve child products
     *
     * @return array
     */
    protected function _getChildProducts()
    {
        return $this->getProduct()->getTypeInstance()->getAssociatedProducts($this->getProduct());
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = array();
        foreach ($this->getChildProducts() as $item) {
            $identities = array_merge($identities, $item->getIdentities());
        }
        return $identities;
    }
}
