<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Action Product Price (percentage) Condition Model
 *
 * @category   Magento
 * @package    Magento_TargetRule
 */
class Magento_TargetRule_Model_Actions_Condition_Product_Special_Price
    extends Magento_TargetRule_Model_Actions_Condition_Product_Special
{
    /**
     * Set rule type
     *
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(Magento_Rule_Model_Condition_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
        $this->setType('Magento_TargetRule_Model_Actions_Condition_Product_Special_Price');
        $this->setValue(100);
    }

    /**
     * Retrieve operator select options array
     *
     * @return array
     */
    protected function _getOperatorOptionArray()
    {
        return array(
            '==' => Mage::helper('Magento_TargetRule_Helper_Data')->__('equal to'),
            '>'  => Mage::helper('Magento_TargetRule_Helper_Data')->__('more'),
            '>=' => Mage::helper('Magento_TargetRule_Helper_Data')->__('equals or greater than'),
            '<'  => Mage::helper('Magento_TargetRule_Helper_Data')->__('less'),
            '<=' => Mage::helper('Magento_TargetRule_Helper_Data')->__('equals or less than')
        );
    }

    /**
     * Set operator options
     *
     * @return Magento_TargetRule_Model_Actions_Condition_Product_Special_Price
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setOperatorOption($this->_getOperatorOptionArray());
        return $this;
    }

    /**
     * Retrieve rule as HTML formated string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('Magento_TargetRule_Helper_Data')->__('Product Price is %s %s%% of Matched Product(s) Price', $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Retrieve SELECT WHERE condition for product collection
     *
     * @param Magento_Catalog_Model_Resource_Product_Collection $collection
     * @param Magento_TargetRule_Model_Index $object
     * @param array $bind
     * @return Zend_Db_Expr
     */
    public function getConditionForCollection($collection, $object, &$bind)
    {
        /* @var $resource Magento_TargetRule_Model_Resource_Index */
        $resource       = $object->getResource();
        $operator       = $this->getOperator();

        $where = $resource->getOperatorBindCondition('price_index.min_price', 'final_price', $operator, $bind,
            array(array('bindPercentOf', $this->getValue())));
        return new Zend_Db_Expr(sprintf('(%s)', $where));
    }
}
