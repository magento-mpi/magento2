<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml Google Content Item Type Country Renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Block_Adminhtml_Types_Renderer_Country
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders Google Content Item Id
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        $iso = $row->getData($this->getColumn()->getIndex());
        return Mage::getSingleton('Magento_GoogleShopping_Model_Config')->getCountryInfo($iso, 'name');
    }
}
