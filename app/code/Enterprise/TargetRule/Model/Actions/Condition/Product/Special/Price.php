<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * TargetRule Action Product Price (percentage) Condition Model
 *
 * @category   Enterprise
 * @package    Enterprise_TargetRule
 */
class Enterprise_TargetRule_Model_Actions_Condition_Product_Special_Price
    extends Enterprise_TargetRule_Model_Actions_Condition_Product_Special
{
    /**
     * Set rule type
     *
     *
     *
     * @param Magento_Adminhtml_Helper_Data $adminhtmlData
     * @param Magento_Rule_Model_Condition_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Helper_Data $adminhtmlData,
        Magento_Rule_Model_Condition_Context $context,
        array $data = array()
    ) {
        parent::__construct($adminhtmlData, $context, $data);
        $this->setType('Enterprise_TargetRule_Model_Actions_Condition_Product_Special_Price');
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
            '==' => __('equal to'),
            '>'  => __('more'),
            '>=' => __('equals or greater than'),
            '<'  => __('less'),
            '<=' => __('equals or less than')
        );
    }

    /**
     * Set operator options
     *
     * @return Enterprise_TargetRule_Model_Actions_Condition_Product_Special_Price
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
            . __('Product Price is %1 %2% of Matched Product(s) Price', $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }

    /**
     * Retrieve SELECT WHERE condition for product collection
     *
     * @param Magento_Catalog_Model_Resource_Product_Collection $collection
     * @param Enterprise_TargetRule_Model_Index $object
     * @param array $bind
     * @return Zend_Db_Expr
     */
    public function getConditionForCollection($collection, $object, &$bind)
    {
        /* @var $resource Enterprise_TargetRule_Model_Resource_Index */
        $resource       = $object->getResource();
        $operator       = $this->getOperator();

        $where = $resource->getOperatorBindCondition('price_index.min_price', 'final_price', $operator, $bind,
            array(array('bindPercentOf', $this->getValue())));
        return new Zend_Db_Expr(sprintf('(%s)', $where));
    }
}
