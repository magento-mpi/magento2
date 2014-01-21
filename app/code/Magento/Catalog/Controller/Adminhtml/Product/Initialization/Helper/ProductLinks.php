<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper;

class ProductLinks 
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Backend\Helper\Js $jsHelper
     */
    public function __construct(\Magento\App\RequestInterface $request, \Magento\Backend\Helper\Js $jsHelper)
    {
        $this->request = $request;
        $this->jsHelper = $jsHelper;
    }

    /**
     * Init product links data (related, upsell, crosssel)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function initializeLinks(\Magento\Catalog\Model\Product $product)
    {
        $links = $this->request->getPost('links');

        if (isset($links['related']) && !$product->getRelatedReadonly()) {
            $product->setRelatedLinkData($this->jsHelper->decodeGridSerializedInput($links['related']));
        }

        if (isset($links['upsell']) && !$product->getUpsellReadonly()) {
            $product->setUpSellLinkData($this->jsHelper->decodeGridSerializedInput($links['upsell']));
        }

        if (isset($links['crosssell']) && !$product->getCrosssellReadonly()) {
            $product->setCrossSellLinkData($this->jsHelper->decodeGridSerializedInput($links['crosssell']));
        }

        return $product;
    }
} 
