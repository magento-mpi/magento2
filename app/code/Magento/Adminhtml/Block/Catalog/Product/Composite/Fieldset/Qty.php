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
 * Adminhtml block for fieldset of product custom options
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Composite\Fieldset;

class Qty extends \Magento\Core\Block\Template
{
    /**
     * Constructor for our block with options
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setIsLastFieldset(true);
    }

    /**
     * Retrieve product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', \Mage::registry('product'));
        }
        $product = $this->getData('product');

        return $product;
    }

    /**
     * Return selected qty
     *
     * @return int
     */
    public function getQtyValue()
    {
        $qty = $this->getProduct()->getPreconfiguredValues()->getQty();
        if (!$qty) {
            $qty = 1;
        }
        return $qty;
    }
}
