<?php
/**
 * Product initialzation helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Controller\Adminhtml\Product\Initialization\Helper\Plugin;

class Configurable
{
    /**
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $productType
     * @param \Magento\App\RequestInterface $request
     */
    public function __construct(
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $productType,
        \Magento\App\RequestInterface $request
    ) {
        $this->productType = $productType;
        $this->request = $request;
    }

    /**
     * Initialize data for configurable product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function afterInitialize(\Magento\Catalog\Model\Product $product)
    {
        $attributes = $this->request->getParam('attributes');
        if (!empty($attributes)) {
            $this->productType->setUsedProductAttributeIds($attributes, $product);

            $product->setNewVariationsAttributeSetId($this->request->getPost('new-variations-attribute-set-id'));
            $associatedProductIds = $this->request->getPost('associated_product_ids', array());
            if ($this->request->getActionName() != 'generateVariations') {
                $generatedProductIds = $this->productType
                    ->generateSimpleProducts($product, $this->request->getPost('variations-matrix', array()));
                $associatedProductIds = array_merge($associatedProductIds, $generatedProductIds);
            }
            $product->setAssociatedProductIds(array_filter($associatedProductIds));

            $product->setCanSaveConfigurableAttributes(
                (bool)$this->request->getPost('affect_configurable_product_attributes')
            );
        }

        return $product;
    }
} 
