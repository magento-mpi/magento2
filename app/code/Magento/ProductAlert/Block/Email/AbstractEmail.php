<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Block\Email;

/**
 * Product Alert Abstract Email Block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractEmail extends \Magento\Framework\View\Element\Template
{
    /**
     * Product collection array
     *
     * @var array
     */
    protected $_products = array();

    /**
     * Current Store scope object
     *
     * @var \Magento\Store\Model\Store
     */
    protected $_store;

    /**
     * @var \Magento\Framework\Filter\Input\MaliciousCode
     */
    protected $_maliciousCode;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Filter\Input\MaliciousCode $maliciousCode
     * @param \Magento\Email\Model\TemplateFactory $emailFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Filter\Input\MaliciousCode $maliciousCode,
        array $data = array()
    ) {
        $this->_maliciousCode = $maliciousCode;
        parent::__construct($context, $data);
    }

    /**
    * Filter malicious code before insert content to email
    *
    * @param  string|array $content
    * @return string|array
    */
    public function getFilteredContent($content)
    {
        return $this->_maliciousCode->filter($content);
    }

    /**
     * Set Store scope
     *
     * @param int|string|\Magento\Store\Model\Website|\Magento\Store\Model\Store $store
     * @return $this
     */
    public function setStore($store)
    {
        if ($store instanceof \Magento\Store\Model\Website) {
            $store = $store->getDefaultStore();
        }
        if (!$store instanceof \Magento\Store\Model\Store) {
            $store = $this->_storeManager->getStore($store);
        }

        $this->_store = $store;

        return $this;
    }

    /**
     * Retrieve current store object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = $this->_storeManager->getStore();
        }
        return $this->_store;
    }

    /**
     * Convert price from default currency to current currency
     *
     * @param float $price
     * @param boolean $format             Format price to currency format
     * @param boolean $includeContainer   Enclose into <span class="price"><span>
     * @return float
     */
    public function formatPrice($price, $format = true, $includeContainer = true)
    {
        return $this->getStore()->convertPrice($price, $format, $includeContainer);
    }

    /**
     * Reset product collection
     *
     * @return void
     */
    public function reset()
    {
        $this->_products = array();
    }

    /**
     * Add product to collection
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    public function addProduct(\Magento\Catalog\Model\Product $product)
    {
        $this->_products[$product->getId()] = $product;
    }

    /**
     * Retrieve product collection array
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->_products;
    }

    /**
     * Get store url params
     *
     * @return array
     */
    protected function _getUrlParams()
    {
        return array('_scope' => $this->getStore(), '_scope_to_url' => true);
    }

    /**
     * @return \Magento\Framework\Pricing\Render
     */
    protected function getPriceRender()
    {
        return $this->_layout->createBlock(
            'Magento\Framework\Pricing\Render',
            '',
            ['data'=> ['price_render_handle' => 'catalog_product_prices']]
        );
    }

    /**
     * Return HTML block with tier price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getPriceRender();
        $price = '';

        if ($priceRender) {
            $price = $priceRender->render(
                $priceType,
                $product,
                $arguments
            );
        }
        return $price;
    }
}
