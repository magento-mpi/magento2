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
 * Device design themes accordion block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design_Accordion_Themes
    extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
{
    /**
     * Getter for accordion item title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->__('Color Themes');
    }

    /**
     * Getter for accordion item is open flag
     *
     * @return bool
     */
    public function getIsOpen()
    {
        return true;
    }

    /**
     * Add theme field
     *
     * @return Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('field_colors', array());
        $this->_addElementTypes($fieldset);
        $fieldset->addField('theme', 'theme', array(
            'name'      => 'theme',
            'themes'    => Mage::helper('Mage_XmlConnect_Helper_Theme')->getAllThemes(),
        ));
        $form->setValues(Mage::helper('Mage_XmlConnect_Helper_Data')->getApplication()->getFormData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
