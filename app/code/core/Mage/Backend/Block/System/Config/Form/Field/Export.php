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
 * Export CSV button for shipping table rates
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Export extends Varien_Data_Form_Element_Abstract
{
    public function getElementHtml()
    {
        $buttonBlock = $this->getForm()->getParent()->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button');

        $params = array(
            'website' => $buttonBlock->getRequest()->getParam('website')
        );

        $url = Mage::helper('Mage_Adminhtml_Helper_Data')->getUrl("*/*/exportTablerates", $params);
        $data = array(
            'label'     => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Export CSV'),
            'onclick'   => "setLocation('" . $url
                . "conditionName/' + $('carriers_tablerate_condition_name').value + '/tablerates.csv' )",
            'class'     => '',
        );

        $html = $buttonBlock->setData($data)->toHtml();

        return $html;
    }
}
