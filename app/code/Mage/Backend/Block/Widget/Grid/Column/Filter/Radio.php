<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Checkbox grid column filter
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget_Grid_Column_Filter_Radio extends Mage_Backend_Block_Widget_Grid_Column_Filter_Select
{
    protected function _getOptions()
    {
        return array(
            array(
                'label' => __('Any'),
                'value' => ''
            ),
            array(
                'label' => __('Yes'),
                'value' => 1
            ),
            array(
                'label' => __('No'),
                'value' => 0
            ),
        );
    }
    
    public function getCondition()
    {
        if ($this->getValue()) {
            return $this->getColumn()->getValue();
        } else {
            return array(
                array('neq'=>$this->getColumn()->getValue()),
                array('is'=>new Zend_Db_Expr('NULL'))
            );
        }
    }
}
