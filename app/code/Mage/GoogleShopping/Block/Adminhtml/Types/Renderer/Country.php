<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml Google Content Item Type Country Renderer
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Block_Adminhtml_Types_Renderer_Country
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders Google Content Item Id
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        $iso = $row->getData($this->getColumn()->getIndex());
        return Mage::getSingleton('Mage_GoogleShopping_Model_Config')->getCountryInfo($iso, 'name');
    }
}
