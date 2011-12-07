<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Locked administrators page
 *
 */
class Enterprise_Pci_Block_Adminhtml_Locks extends Mage_Adminhtml_Block_Widget_Container
{
    /**
     * Header text getter
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('Enterprise_Pci_Helper_Data')->__('Locked administrators');
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getLayout()->createBlock('Enterprise_Pci_Block_Adminhtml_Locks_Grid')
            ->toHtml();
    }
}
