<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System Convert History action renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Convert_Profile_Edit_Renderer_Action
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $values = array(
            'create' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Create'),
            'run'    => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Run'),
            'update' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Update'),
        );
        $value = $row->getData($this->getColumn()->getIndex());
        return $values[$value];
    }
}
