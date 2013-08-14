<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product Tax Class
 */
class Magento_Tax_Model_Class_Type_Product
    extends Magento_Tax_Model_Class_TypeAbstract
    implements Magento_Tax_Model_Class_Type_Interface
{
    /**
     * @var Magento_Catalog_Model_Product
     */
    protected $_modelProduct;

    /**
     * @var Magento_Tax_Helper_Data
     */
    protected $_helper;

    /**
     * Class Type
     *
     * @var string
     */
    protected $_classType = Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT;

    /**
     * @param Magento_Tax_Model_Calculation_Rule $calculationRule
     * @param Magento_Catalog_Model_Product $modelProduct
     * @param Magento_Tax_Helper_Data $helper
     * @param array $data
     */
    public function __construct(
        Magento_Tax_Model_Calculation_Rule $calculationRule,
        Magento_Catalog_Model_Product $modelProduct,
        Magento_Tax_Helper_Data $helper,
        array $data = array()
    ) {
        parent::__construct($calculationRule, $data);
        $this->_modelProduct = $modelProduct;
        $this->_helper = $helper;
    }

    /**
     * Get Products with this tax class
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getAssignedToObjects()
    {
        return $this->_modelProduct
            ->getCollection()
            ->addAttributeToFilter('tax_class_id', $this->getId());
    }

    /**
     * Get Name of Objects that use this Tax Class Type
     *
     * @return string
     */
    public function getObjectTypeName()
    {
        return $this->_helper->__('product');
    }
}
