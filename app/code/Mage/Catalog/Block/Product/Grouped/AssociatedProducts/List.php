<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Products in grouped grid
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_Grouped_AssociatedProducts_List extends Mage_Backend_Block_Template
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
     * @param Mage_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $storeManager
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        parent::__construct($context, $data);
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
        /** @var $product Mage_Catalog_Model_Product */
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
