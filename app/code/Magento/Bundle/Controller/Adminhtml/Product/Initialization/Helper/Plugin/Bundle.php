<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Controller\Adminhtml\Product\Initialization\Helper\Plugin;

class Bundle
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\App\RequestInterface $request
     */
    public function __construct(\Magento\App\RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Setting Bundle Items Data to product for father processing
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function afterInitialize(\Magento\Catalog\Model\Product $product)
    {
        if (($items = $this->request->getPost('bundle_options')) && !$product->getCompositeReadonly()) {
            $product->setBundleOptionsData($items);
        }

        if (($selections = $this->request->getPost('bundle_selections')) && !$product->getCompositeReadonly()) {
            $product->setBundleSelectionsData($selections);
        }

        if ($product->getPriceType() == '0' && !$product->getOptionsReadonly()) {
            $product->setCanSaveCustomOptions(true);
            if ($customOptions = $product->getProductOptions()) {
                foreach (array_keys($customOptions) as $key) {
                    $customOptions[$key]['is_delete'] = 1;
                }
                $product->setProductOptions($customOptions);
            }
        }

        $product->setCanSaveBundleSelections(
            (bool)$this->request->getPost('affect_bundle_product_selections') && !$product->getCompositeReadonly()
        );

        return $product;
    }
} 
