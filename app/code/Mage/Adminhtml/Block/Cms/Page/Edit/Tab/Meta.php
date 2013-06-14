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
 * Customer account form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Meta
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Mage_Cms::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $model = Mage::registry('cms_page');

        $fieldset = $form->addFieldset('meta_fieldset', array('legend' => Mage::helper('Mage_Cms_Helper_Data')->__('Meta Data'), 'class' => 'fieldset-wide'));

        $fieldset->addField('meta_keywords', 'textarea', array(
            'name' => 'meta_keywords',
            'label' => Mage::helper('Mage_Cms_Helper_Data')->__('Keywords'),
            'title' => Mage::helper('Mage_Cms_Helper_Data')->__('Meta Keywords'),
            'disabled'  => $isElementDisabled
        ));

        $fieldset->addField('meta_description', 'textarea', array(
            'name' => 'meta_description',
            'label' => Mage::helper('Mage_Cms_Helper_Data')->__('Description'),
            'title' => Mage::helper('Mage_Cms_Helper_Data')->__('Meta Description'),
            'disabled'  => $isElementDisabled
        ));

        Mage::dispatchEvent('adminhtml_cms_page_edit_tab_meta_prepare_form', array('form' => $form));

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Mage_Cms_Helper_Data')->__('Meta Data');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('Mage_Cms_Helper_Data')->__('Meta Data');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
