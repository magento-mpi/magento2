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
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Catalog_Model_Resource_Product $productResource
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Set_CollectionFactory $eavEntitySetFactory
     * @param Magento_Backend_Helper_Data $backendData
     * @param Magento_Rule_Model_Condition_Context $context
     * @param Magento_Eav_Model_Config $config
     * @param Magento_Catalog_Model_Product $product
     * @param Magento_Catalog_Model_Resource_Product $productResource
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Set_Collection $attrSetCollection
     * @param array $data
     */
    public function __construct(
        Magento_Eav_Model_Config $eavConfig,
        Magento_Catalog_Model_Resource_Product $productResource,
        Magento_Eav_Model_Resource_Entity_Attribute_Set_CollectionFactory $eavEntitySetFactory,
        Magento_Backend_Helper_Data $backendData,
        Magento_Rule_Model_Condition_Context $context,
        Magento_Eav_Model_Config $config,
        Magento_Catalog_Model_Product $product,
        Magento_Catalog_Model_Resource_Product $productResource,
        Magento_Eav_Model_Resource_Entity_Attribute_Set_Collection $attrSetCollection,
        array $data = array()
    ) {
        parent::__construct($eavConfig, $productResource, $eavEntitySetFactory, $backendData, $context, $config, $product, $productResource, $attrSetCollection, $data);
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
            . __('Product Price is %1 %2% of Matched Product(s) Price', $this->getOperatorElementHtml(), $this->getValueElementHtml())
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
