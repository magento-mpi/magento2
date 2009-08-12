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
 * @package    Enterprise_Customer
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Form Type Theme Grid Renderer
 *
 * @category   Enterprise
 * @package    Enterprise_Customer
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Formtype_Grid_Renderer_Theme
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $options = $this->getColumn()->getOptions();
        $value   = $this->_getValue($row);
        if ($value == '') {
            $value = 'all';
        }

        return $this->_getValueLabel($options, $value);
    }

    /**
     * Retrieve value label from options array
     *
     * @param array $options
     * @param string $value
     * @return mixed
     */
    protected function _getValueLabel($options, $value)
    {
        if (empty($options) || !is_array($options)) {
            return false;
        }

        foreach ($options as $option) {
            if (is_array($option['value'])) {
                $label = $this->_getValueLabel($option['value'], $value);
                if ($label) {
                    return $label;
                }
            } else {
                if ($option['value'] == $value) {
                    return $option['label'];
                }
            }
        }

        return false;
    }
}