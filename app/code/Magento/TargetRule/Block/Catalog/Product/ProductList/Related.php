<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Catalog Product List Related Block
 *
 * @category   Magento
 * @package    Magento_TargetRule
 */
namespace Magento\TargetRule\Block\Catalog\Product\ProductList;

class Related
    extends \Magento\TargetRule\Block\Catalog\Product\ProductList\AbstractProductList
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\TargetRule\Model\Resource\Index $index
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\TargetRule\Helper\Data $targetRuleData
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\TargetRule\Model\IndexFactory $indexFactory
     * @param \Magento\Checkout\Model\Cart $cart
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\TargetRule\Model\Resource\Index $index,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\TargetRule\Helper\Data $targetRuleData,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\TargetRule\Model\IndexFactory $indexFactory,
        \Magento\Checkout\Model\Cart $cart,
        array $data = array()
    ) {
        $this->_cart = $cart;
        parent::__construct(
            $storeManager, $catalogConfig, $index, $coreRegistry, $targetRuleData, $taxData, $catalogData,
            $coreData, $context, $productCollectionFactory, $visibility, $indexFactory, $data
        );
    }


    /**
     * Retrieve Catalog Product List Type identifier
     *
     * @return int
     */
    public function getProductListType()
    {
        return \Magento\TargetRule\Model\Rule::RELATED_PRODUCTS;
    }

    /**
     * Retrieve array of exclude product ids
     * Rewrite for exclude shopping cart products
     *
     * @return array
     */
    public function getExcludeProductIds()
    {
        if (is_null($this->_excludeProductIds)) {
            $cartProductIds = $this->_cart->getProductIds();
            $this->_excludeProductIds = array_merge($cartProductIds, array($this->getProduct()->getEntityId()));
        }
        return $this->_excludeProductIds;
    }
}
