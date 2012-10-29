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
 * Sitemap edit form container
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sitemap_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Init container
     */
    protected function _construct()
    {
        $this->_objectId = 'sitemap_id';
        $this->_controller = 'sitemap';

        parent::_construct();

        $this->_addButton('generate', array(
            'label'   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Save & Generate'),
            'onclick' => "$('generate').value=1; editForm.submit();",
            'class'   => 'add',
        ));
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('sitemap_sitemap')->getId()) {
            return Mage::helper('Mage_Sitemap_Helper_Data')->__('Edit Sitemap');
        }
        else {
            return Mage::helper('Mage_Sitemap_Helper_Data')->__('New Sitemap');
        }
    }
}
