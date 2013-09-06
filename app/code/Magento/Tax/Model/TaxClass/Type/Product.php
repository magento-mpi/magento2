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
class Magento_Tax_Model_TaxClass_Type_Product
    extends Magento_Tax_Model_TaxClass_TypeAbstract
    implements Magento_Tax_Model_TaxClass_Type_Interface
{
    /**
     * @var Magento_Catalog_Model_Product
     */
    protected $_modelProduct;

    /**
     * Class Type
     *
     * @var string
     */
    protected $_classType = Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT;

    /**
     * @param Magento_Tax_Model_Calculation_Rule $calculationRule
     * @param Magento_Catalog_Model_Product $modelProduct
     * @param array $data
     */
    public function __construct(
        Magento_Tax_Model_Calculation_Rule $calculationRule,
        Magento_Catalog_Model_Product $modelProduct,
        array $data = array()
    ) {
        parent::__construct($calculationRule, $data);
        $this->_modelProduct = $modelProduct;
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
        return __('product');
    }
}
