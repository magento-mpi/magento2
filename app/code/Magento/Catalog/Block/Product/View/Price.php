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
 * Catalog product price block
 */
class Magento_Catalog_Block_Product_View_Price extends Magento_Core_Block_Template
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

    public function getPrice()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product->getFormatedPrice();
    }
}
