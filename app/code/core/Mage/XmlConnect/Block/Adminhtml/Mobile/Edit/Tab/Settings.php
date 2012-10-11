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
 * Tab for Settings Management
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Settings
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareLayout()
    {
        $this->addChild('continue_button', 'Mage_Adminhtml_Block_Widget_Button', array(
            'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Continue'),
            'onclick'   => "if (editForm.submit()) { return false }",
            'class'     => 'save'
        ));
        return parent::_prepareLayout();
    }

    /**
     * Prepare form before rendering HTML
     * Setting Form Fieldsets and fields
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('app_');
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $this->__('Device Information')));

        $fieldset->addField('type', 'select', array(
                'name'      => 'type',
                'label'     => $this->__('Device Type'),
                'title'     => $this->__('Device Type'),
                'values'    => Mage::helper('Mage_XmlConnect_Helper_Data')->getDeviceTypeOptions(),
                'required'  => true
        ));

        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Tab label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Settings');
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Settings');
    }

    /**
     * Check if tab can be shown
     *
     * @return bool
     */
    public function canShowTab()
    {
        return (bool) Mage::getSingleton('Mage_Adminhtml_Model_Session')->getNewApplication();
    }

    /**
     * Check if tab hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
