<?php
/**
 * Catalog grouped product info block
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GroupedProduct\Block\Product\View\Type;

class Grouped extends \Magento\Catalog\Block\Product\View\AbstractView
{
    public function getAssociatedProducts()
    {
        return $this->getProduct()->getTypeInstance()
            ->getAssociatedProducts($this->getProduct());
    }

    /**
     * Set preconfigured values to grouped associated products
     *
     * @return \Magento\GroupedProduct\Block\Product\View\Type\Grouped
     */
    public function setPreconfiguredValue()
    {
        $configValues = $this->getProduct()->getPreconfiguredValues()->getSuperGroup();
        if (is_array($configValues)) {
            $associatedProducts = $this->getAssociatedProducts();
            foreach ($associatedProducts as $item) {
                if (isset($configValues[$item->getId()])) {
                    $item->setQty($configValues[$item->getId()]);
                }
            }
        }
        return $this;
    }

    /**
     * Returns product tier price block html
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getTierPriceHtml($product = null)
    {
        if (is_null($product)) {
            $product = $this->getProduct();
        }
        return $this->_getPriceBlock($product->getTypeId())
            ->setTemplate($this->getTierPriceTemplate())
            ->setProduct($product)
            ->setListClass('tier prices grouped items')
            ->setShowDetailedPrice(false)
            ->setCanDisplayQty(false)
            ->toHtml();
    }
}
