<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Rule General Information Tab
 *
 * @category Magento
 * @package Magento_Adminhtml
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Promo_Catalog_Edit_Tab_Main
    extends Magento_Adminhtml_Block_Widget_Form
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Magento_CatalogRule_Helper_Data')->__('Rule Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('Magento_CatalogRule_Helper_Data')->__('Rule Information');
    }

    /**
     * Returns status flag about this tab can be showed or not
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

    protected function _prepareForm()
    {
        $model = Mage::registry('current_promo_catalog_rule');

        $form = new Magento_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => Mage::helper('Magento_CatalogRule_Helper_Data')->__('General Information'))
        );

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Rule Name'),
            'title' => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Description'),
            'title' => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Description'),
            'style' => 'height: 100px;',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Status'),
            'title'     => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Active'),
                '0' => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Inactive'),
            ),
        ));

        if (Mage::app()->isSingleStoreMode()) {
            $websiteId = Mage::app()->getStore(true)->getWebsiteId();
            $fieldset->addField('website_ids', 'hidden', array(
                'name'     => 'website_ids[]',
                'value'    => $websiteId
            ));
            $model->setWebsiteIds($websiteId);
        } else {
            $field = $fieldset->addField('website_ids', 'multiselect', array(
                'name'     => 'website_ids[]',
                'label'     => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Websites'),
                'title'     => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Websites'),
                'required' => true,
                'values'   => Mage::getSingleton('Magento_Core_Model_System_Store')->getWebsiteValuesForForm(),
            ));
            $renderer = $this->getLayout()->createBlock('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
        }

        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'name'      => 'customer_group_ids[]',
            'label'     => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Customer Groups'),
            'title'     => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Customer Groups'),
            'required'  => true,
            'values'    => Mage::getResourceModel('Magento_Customer_Model_Resource_Group_Collection')->toOptionArray()
        ));

        $dateFormat = Mage::app()->getLocale()->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_SHORT);
        $fieldset->addField('from_date', 'date', array(
            'name'   => 'from_date',
            'label'  => Mage::helper('Magento_CatalogRule_Helper_Data')->__('From Date'),
            'title'  => Mage::helper('Magento_CatalogRule_Helper_Data')->__('From Date'),
            'image'  => $this->getViewFileUrl('images/grid-cal.gif'),
            'input_format' => Magento_Date::DATE_INTERNAL_FORMAT,
            'date_format' => $dateFormat
        ));
        $fieldset->addField('to_date', 'date', array(
            'name'   => 'to_date',
            'label'  => Mage::helper('Magento_CatalogRule_Helper_Data')->__('To Date'),
            'title'  => Mage::helper('Magento_CatalogRule_Helper_Data')->__('To Date'),
            'image'  => $this->getViewFileUrl('images/grid-cal.gif'),
            'input_format' => Magento_Date::DATE_INTERNAL_FORMAT,
            'date_format' => $dateFormat
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => Mage::helper('Magento_CatalogRule_Helper_Data')->__('Priority'),
        ));

        $form->setValues($model->getData());

        //$form->setUseContainer(true);

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        Mage::dispatchEvent('adminhtml_promo_catalog_edit_tab_main_prepare_form', array('form' => $form));

        return parent::_prepareForm();
    }
}
