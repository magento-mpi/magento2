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
class Magento_TargetRule_Block_Catalog_Product_List_Related
    extends Magento_TargetRule_Block_Catalog_Product_List_Abstract
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    /**
     * @var Magento_Checkout_Model_Cart
     */
    protected $_cart;

    /**
     * @param Magento_Checkout_Model_Cart $cart
     * @param Magento_TargetRule_Model_Resource_Index $index
     * @param Magento_Catalog_Model_Resource_Product_CollectionFactory $productCollectionFactory
     * @param Magento_Catalog_Model_Product_Visibility $visibility
     * @param Magento_TargetRule_Model_IndexFactory $indexFactory
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_TargetRule_Helper_Data $targetRuleData
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function __construct(
        Magento_Checkout_Model_Cart $cart,
        Magento_Catalog_Model_Resource_Product_CollectionFactory $productCollectionFactory,
        Magento_Catalog_Model_Product_Visibility $visibility,
        Magento_TargetRule_Model_IndexFactory $indexFactory,
        Magento_TargetRule_Model_Resource_Index $index,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_TargetRule_Helper_Data $targetRuleData,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_cart = $cart;
        parent::__construct(
            $productCollectionFactory, $visibility, $indexFactory, $index, $coreRegistry,
            $targetRuleData, $taxData, $catalogData, $coreData, $context, $data
        );
    }


    /**
     * Retrieve Catalog Product List Type identifier
     *
     * @return int
     */
    public function getProductListType()
    {
        return Magento_TargetRule_Model_Rule::RELATED_PRODUCTS;
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
