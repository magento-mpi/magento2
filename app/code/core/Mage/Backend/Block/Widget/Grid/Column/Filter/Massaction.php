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
 * Massaction grid column filter
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget_Grid_Column_Filter_Massaction extends Mage_Backend_Block_Widget_Grid_Column_Filter_Checkbox
{
    public function getCondition()
    {
        if ($this->getValue()) {
            return array('in'=> ( $this->getColumn()->getSelected() ? $this->getColumn()->getSelected() : array(0) ));
        }
        else {
            return array('nin'=> ( $this->getColumn()->getSelected() ? $this->getColumn()->getSelected() : array(0) ));
        }
    }
}
