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
namespace Magento\Adminhtml\Block\Catalog\Product\Composite;

class Configure extends \Magento\Adminhtml\Block\Widget
{
    protected $_product;

    protected $_template = 'catalog/product/composite/configure.phtml';

    /**
     * Retrieve product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            if (\Mage::registry('current_product')) {
                $this->_product = \Mage::registry('current_product');
            } else {
                $this->_product = \Mage::getSingleton('Magento\Catalog\Model\Product');
            }
        }
        return $this->_product;
    }

    /**
     * Set product object
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Adminhtml\Block\Catalog\Product\Composite\Configure
     */
    public function setProduct(\Magento\Catalog\Model\Product $product = null)
    {
        $this->_product = $product;
        return $this;
    }
}
