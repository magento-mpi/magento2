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
 */
class Magento_Adminhtml_Block_Catalog_Product_Composite_Fieldset_Qty extends Magento_Core_Block_Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

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
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->_coreRegistry->registry('product'));
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
