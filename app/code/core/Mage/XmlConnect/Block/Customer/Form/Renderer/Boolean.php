<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer boolean select field form xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Form_Renderer_Boolean extends Mage_XmlConnect_Block_Customer_Form_Renderer_Select
{
    /**
     * Return array of select options
     *
     * @return array
     */
    public function getOptions()
    {
        return array(
            array('value' => '', 'label' => ''),
            array('value' => '0', 'label' => Mage::helper('Enterprise_Customer_Helper_Data')->__('No')),
            array('value' => '1', 'label' => Mage::helper('Enterprise_Customer_Helper_Data')->__('Yes'))
        );
    }
}
