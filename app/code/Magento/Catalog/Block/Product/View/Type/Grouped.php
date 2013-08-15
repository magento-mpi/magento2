<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog grouped product info block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Product_View_Type_Grouped extends Magento_Catalog_Block_Product_View_Abstract
{
    public function getAssociatedProducts()
    {
        return $this->getProduct()->getTypeInstance()
            ->getAssociatedProducts($this->getProduct());
    }


    /**
     * Set preconfigured values to grouped associated products
     *
     * @return Magento_Catalog_Block_Product_View_Type_Grouped
     */
    public function setPreconfiguredValue() {
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
}
