<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product\Initialization;

class Helper
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var StockDataFilter
     */
    protected $stockFilter;

    /**
     * @var \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks
     */
    protected $productLinks;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param StockDataFilter $stockFilter
     * @param \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $productLinks
     * @param \Magento\Backend\Helper\Js $jsHelper
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        StockDataFilter $stockFilter,
        \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $productLinks,
        \Magento\Backend\Helper\Js $jsHelper
    ) {
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->stockFilter = $stockFilter;
        $this->productLinks = $productLinks;
        $this->jsHelper = $jsHelper;
    }

    /**
     * Initialize product before saving
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function initialize(\Magento\Catalog\Model\Product $product)
    {
        $productData = $this->request->getPost('product');

        if ($productData) {
            $stockData = isset($productData['stock_data']) ? $productData['stock_data'] : array();
            $productData['stock_data'] = $this->stockFilter->filter($stockData);
        }

        foreach (array('category_ids', 'website_ids') as $field) {
            if (!isset($productData[$field])) {
                $productData[$field] = array();
            }
        }

        $wasLockedMedia = false;
        if ($product->isLockedAttribute('media')) {
            $product->unlockAttribute('media');
            $wasLockedMedia = true;
        }

        $product->addData($productData);

        if ($wasLockedMedia) {
            $product->lockAttribute('media');
        }

        if ($this->storeManager->hasSingleStore()) {
            $product->setWebsiteIds(array($this->storeManager->getStore(true)->getWebsite()->getId()));
        }

        /**
         * Create Permanent Redirect for old URL key
         */
        if ($product->getId() && isset($productData['url_key_create_redirect'])) {
            $product->setData('save_rewrites_history', (bool)$productData['url_key_create_redirect']);
        }

        /**
         * Check "Use Default Value" checkboxes values
         */
        $useDefaults = $this->request->getPost('use_default');
        if ($useDefaults) {
            foreach ($useDefaults as $attributeCode) {
                $product->setData($attributeCode, false);
            }
        }

        $links = $this->request->getPost('links');
        $links = is_array($links) ? $links : array();
        $linkTypes = array('related', 'upsell', 'crosssell');
        foreach ($linkTypes as $type) {
            if (isset($links[$type])) {
                $links[$type] = $this->jsHelper->decodeGridSerializedInput($links[$type]);
            }
        }
        $product = $this->productLinks->initializeLinks($product, $links);

        /**
         * Initialize product options
         */
        if (isset($productData['options']) && !$product->getOptionsReadonly()) {
            $product->setProductOptions($productData['options']);
        }

        $product->setCanSaveCustomOptions(
            (bool)$this->request->getPost('affect_product_custom_options') && !$product->getOptionsReadonly()
        );

        return $product;
    }
}
