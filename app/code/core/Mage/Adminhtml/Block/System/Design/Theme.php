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
 *  Page block
 */
class Mage_Adminhtml_Block_System_Design_Theme extends Mage_Backend_Block_Template
{
    /**
     * Prepare controls on page
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('add_new_button',
            $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button')
                ->setData(array(
                'label'   => Mage::helper('Mage_Core_Helper_Data')->__('Add Theme'),
                'onclick' => "setLocation('".$this->getUrl('*/*/new')."')",
                'class'   => 'add'
            ))
        );

        return parent::_prepareLayout();
    }

    /**
     * Prepare header for page
     *
     * @return string
     */
    public function getHeader()
    {
        return Mage::helper('Mage_Core_Helper_Data')->__('Themes');
    }
}
