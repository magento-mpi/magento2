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
 * @category   Enterprise
 * @package    Enterprise_TargetRule
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_TargetRule_Block_Adminhtml_Product extends Mage_Adminhtml_Block_Widget
{
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
        $return = $this->_getValue('rule_based_positions');
        if (null === $return) {
            $return = $this->getDefaultValue('rule_based_positions');
        }
        return $return;
    }

    /**
     * Get value of Position Behavior
     *
     * @return mixed
     */
    public function getPositionBehavior()
    {
        $return = $this->_getValue('position_behavior');
        if (null === $return) {
            $return = $this->getDefaultValue('position_behavior');
        }
        return $return;
    }

    /**
     * Get value from Product model
     *
     * @param string $var
     * @return mixed
     */
    protected function _getValue($var)
    {
        $var = str_replace(' ', '', ucwords(str_replace('_', ' ', $var)));
        $_getFunction = 'get' . ucfirst($this->getFormPrefix()) . 'Targetrule' . $var;
        return Mage::registry('current_product')->$_getFunction();
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

    /**
     * Get default value
     *
     * @param string $value
     * @return mixed
     */
    public function getDefaultValue($value)
    {
        $value = $this->getFormPrefix() .'_'. $value;
        return Mage::getStoreConfig(Enterprise_TargetRule_Model_Rule::CONFIG_VALUES_XPATH . $value);
    }
}
