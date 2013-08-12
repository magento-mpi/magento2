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
class Enterprise_Pci_Block_Adminhtml_Locks extends Magento_Backend_Block_Widget_Grid_Container
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
     * Produce buttons HTML
     *
     * @param string $region
     * @return string
     */
    public function getButtonsHtml($region = null)
    {
        return '';
    }
}
