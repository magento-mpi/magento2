<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product view other block
 */
class Magento_Review_Block_Product_View_Other extends Magento_Core_Block_Template
{
    /**
     * @var Magento_Core_Model_Registry
     */
    protected $_registry;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_registry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return Magento_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_registry->registry('product');
    }
}
