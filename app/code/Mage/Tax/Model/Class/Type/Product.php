<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product Tax Class
 */
class Mage_Tax_Model_Class_Type_Product
    extends Mage_Tax_Model_Class_TypeAbstract
    implements Mage_Tax_Model_Class_Type_Interface
{
    /**
     * @var Mage_Catalog_Model_Product
     */
    protected $_modelProduct;

    /**
     * Class Type
     *
     * @var string
     */
    protected $_classType = Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT;

    /**
     * @param Mage_Tax_Model_Calculation_Rule $calculationRule
     * @param Mage_Catalog_Model_Product $modelProduct
     * @param array $data
     */
    public function __construct(
        Mage_Tax_Model_Calculation_Rule $calculationRule,
        Mage_Catalog_Model_Product $modelProduct,
        array $data = array()
    ) {
        parent::__construct($calculationRule, $data);
        $this->_modelProduct = $modelProduct;
    }

    /**
     * Get Products with this tax class
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
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
