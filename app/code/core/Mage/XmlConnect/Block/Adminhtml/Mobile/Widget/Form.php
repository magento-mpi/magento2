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
 * Xmlconnect widget form block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Add color chooser to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldName
     * @param string $title
     */
    protected function addColor($fieldset, $fieldName, $title)
    {
        $fieldset->addField($fieldName, 'color', array(
            'name'      => $fieldName,
            'label'     => $title,
        ));
    }

    /**
     * Add image uploader to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldName
     * @param string $title
     * @param string|null $note
     * @param string $default
     * @param boolean $required
     */
    public function addImage($fieldset, $fieldName, $title, $note = null, $default = '', $required = false)
    {
        $fieldset->addField($fieldName, 'image', array(
            'name'      => $fieldName,
            'label'     => $title,
            'note'      => $note,
            'default_value' => $default,
            'required'  => $required,
        ));
    }

    /**
     * Add font selector to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldPrefix
     * @param string $title
     */
    public function addFont($fieldset, $fieldPrefix, $title)
    {
        $element = $fieldset->addField($fieldPrefix, 'font', array(
            'name'      => $fieldPrefix,
            'label'     => $title,
        ));

        $element->initFields(array(
            'name'      => $fieldPrefix,
            'fontNames' => Mage::helper('Mage_XmlConnect_Helper_Data')->getDeviceHelper()->getFontList(),
            'fontSizes' => Mage::helper('Mage_XmlConnect_Helper_Data')->getDeviceHelper()->getFontSizes(),
        ));
    }

    /**
     * Configure image element type
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        $config = Mage::getConfig();
        return array(
            'image' => $config->getBlockClassName('Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Image'),
            'font'  => $config->getBlockClassName('Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Font'),
            'color' => $config->getBlockClassName('Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Color'),
            'tabs'  => $config->getBlockClassName('Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Tabs'),
            'theme' => $config->getBlockClassName('Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Theme'),
            'page'  => $config->getBlockClassName('Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Page'),
            'addrow'=> $config->getBlockClassName('Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Addrow'),
            'datetime' => $config->getBlockClassName('Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Datetime'),
        );
    }
}
