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
 * Products in grouped grid
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Product_Grouped_AssociatedProducts_List extends Magento_Backend_Block_Template
{
    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * Registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_registry;

    /**
     * @inheritdoc
     *
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Registry $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        parent::__construct($context, $coreStoreConfig, $data);
        $this->_storeManager = $storeManager;
        $this->_registry = $registry;
    }

    /**
     * Retrieve grouped products
     *
     * @return array
     */
    public function getAssociatedProducts()
    {
        /** @var $product Magento_Catalog_Model_Product */
        $product = $this->_registry->registry('current_product');
        $associatedProducts = $product->getTypeInstance()
            ->getAssociatedProducts($product);
        $products = array();

        foreach ($associatedProducts as $product) {
            $products[] = array(
                'id'        => $product->getId(),
                'sku'       => $product->getSku(),
                'name'      => $product->getName(),
                'price'     => $this->_storeManager->getStore()->formatPrice($product->getPrice(), false),
                'qty'       => $product->getQty(),
                'position'  => $product->getPosition(),
            );
        }
        return $products;
    }
}
