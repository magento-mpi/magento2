<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Massaction grid column filter
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_Widget_Grid_Column_Filter_Massaction
    extends Magento_Backend_Block_Widget_Grid_Column_Filter_Checkbox
{
    public function getCondition()
    {
        if ($this->getValue()) {
            return array('in'=> ( $this->getColumn()->getSelected() ? $this->getColumn()->getSelected() : array(0) ));
        } else {
            return array('nin'=> ( $this->getColumn()->getSelected() ? $this->getColumn()->getSelected() : array(0) ));
        }
    }
}
