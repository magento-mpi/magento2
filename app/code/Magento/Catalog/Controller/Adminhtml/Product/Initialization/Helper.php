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
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var StockDataFilter
     */
    protected $stockFilter;

    /**
     * @var Helper\ProductLinks
     */
    protected $productLinks;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Backend\Helper\Js $jsHelper
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param StockDataFilter $stockFilter
     * @param Helper\ProductLinks $productLinks
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        StockDataFilter $stockFilter,
        Helper\ProductLinks $productLinks
    ) {
        $this->request = $request;
        $this->jsHelper = $jsHelper;
        $this->storeManager = $storeManager;
        $this->stockFilter = $stockFilter;
        $this->productLinks = $productLinks;
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

        if ($productData && isset($productData['stock_data'])) {
            $productData['stock_data'] = $this->stockFilter->filter($productData['stock_data']);
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

        $product = $this->productLinks->initializeLinks($product);

        /**
         * Initialize product options
         */
        if (isset($productData['options']) && !$product->getOptionsReadonly()) {
            $product->setProductOptions($productData['options']);
        }

        $product->setCanSaveCustomOptions(
            (bool)$this->request->getPost('affect_product_custom_options')
            && !$product->getOptionsReadonly()
        );

        return $product;
    }
} 
