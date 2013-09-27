<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml catalog product composite configure block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Composite_Configure extends Magento_Adminhtml_Block_Widget
{
    protected $_productInstance;

    protected $_template = 'catalog/product/composite/configure.phtml';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento_Catalog_Model_Product
     */
    protected $_product;

    /**
     * @param Magento_Catalog_Model_Product $product
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_Product $product,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_product = $product;
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve product object
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_productInstance) {
            if ($this->_coreRegistry->registry('current_product')) {
                $this->_productInstance = $this->_coreRegistry->registry('current_product');
            } else {
                $this->_productInstance = $this->_product;
            }
        }
        return $this->_productInstance;
    }

    /**
     * Set product object
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Adminhtml_Block_Catalog_Product_Composite_Configure
     */
    public function setProduct(Magento_Catalog_Model_Product $product = null)
    {
        $this->_productInstance = $product;
        return $this;
    }
}
