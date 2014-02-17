<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Controller\Adminhtml\Product\Initialization\Helper\ProductLinks\Plugin;

class Grouped
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
     * Initialize grouped product links
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function afterInitializeLinks(\Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper\ProductLinks $subject, \Magento\Catalog\Model\Product $product)
    {
        $links = $this->request->getPost('links');

        if (isset($links['grouped']) && !$product->getGroupedReadonly()) {
            $product->setGroupedLinkData((array)$links['grouped']);
        }

        return $product;
    }
} 
