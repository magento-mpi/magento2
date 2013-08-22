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
    protected $_product;

    protected $_template = 'catalog/product/composite/configure.phtml';

    /**
     * Retrieve product object
     *
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            if (Mage::registry('current_product')) {
                $this->_product = Mage::registry('current_product');
            } else {
                $this->_product = Mage::getSingleton('Magento_Catalog_Model_Product');
            }
        }
        return $this->_product;
    }

    /**
     * Set product object
     *
     * @param Magento_Catalog_Model_Product $product
     * @return Magento_Adminhtml_Block_Catalog_Product_Composite_Configure
     */
    public function setProduct(Magento_Catalog_Model_Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }
}
