<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_TargetRule_Block_Adminhtml_Product extends Mage_Adminhtml_Block_Widget
{
    /**
     * Retrieve TargetRule Data Helper
     *
     * @return Enterprise_TargetRule_Helper_Data
     */
    protected function _getRuleHelper()
    {
        return Mage::helper('enterprise_targetrule');
    }

    /**
     * Retrieve Product List Type by current Form Prefix
     *
     * @return int
     */
    protected function _getProductListType()
    {
        $listType = '';
        switch ($this->getFormPrefix()) {
            case 'related':
                $listType = Enterprise_TargetRule_Model_Rule::RELATED_PRODUCTS;
                break;
            case 'upsell':
                $listType = Enterprise_TargetRule_Model_Rule::UP_SELLS;
                break;
            case 'crosssell':
                $listType = Enterprise_TargetRule_Model_Rule::CROSS_SELLS;
                break;
        }
        return $listType;
    }

    /**
     * Retrieve current edit product instance
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Get data for Position Behavior selector
     *
     * @return array
     */
    public function getPositionBehaviorOptions()
    {
        return Mage::getModel('enterprise_targetrule/source_position')->toOptionArray();
    }

    /**
     * Get value of Rule Based Positions
     *
     * @return mixed
     */
    public function getRuleBasedPositions()
    {
        $position = $this->_getValue('rule_based_positions');
        if (is_null($position)) {
            $position = $this->_getRuleHelper()->getMaximumNumberOfProduct($this->_getProductListType());
        }
        return $position;
    }

    /**
     * Get value of Position Behavior
     *
     * @return mixed
     */
    public function getPositionBehavior()
    {
        $show = $this->_getValue('position_behavior');
        if (is_null($show)) {
            $show = $this->_getRuleHelper()->getShowProducts($this->_getProductListType());
        }
        return $show;
    }

    /**
     * Get value from Product model
     *
     * @param string $var
     * @return mixed
     */
    protected function _getValue($field)
    {
        return $this->_getProduct()->getDataUsingMethod($this->getFieldName($field));
    }

    /**
     * Get name of the field
     *
     * @param string $field
     * @return string
     */
    public function getFieldName($field)
    {
        return $this->getFormPrefix() . '_targetrule_' . $field;
    }

    /**
     * Define is value should me marked as default
     *
     * @param string $value
     * @return bool
     */
    public function isDefault($value)
    {
        return ($this->_getValue($value) === null) ? true : false;
    }
}
