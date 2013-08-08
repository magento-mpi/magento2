<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * General Properties tab of customer segment configuration
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_General
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare general properties form
     *
     * @return Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_customer_segment');

        $form = new Magento_Data_Form();

        $form->setHtmlIdPrefix('segment_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('General Properties')
        ));

        if ($model->getId()) {
            $fieldset->addField('segment_id', 'hidden', array(
                'name' => 'segment_id'
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Segment Name'),
            'required' => true
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Description'),
            'style' => 'height: 100px;'
        ));

        if (Mage::app()->isSingleStoreMode()) {
            $websiteId = Mage::app()->getStore(true)->getWebsiteId();
            $fieldset->addField('website_ids', 'hidden', array(
                'name'     => 'website_ids[]',
                'value'    => $websiteId
            ));
            $model->setWebsiteIds($websiteId);
        } else {
            $fieldset->addField('website_ids', 'multiselect', array(
                'name'     => 'website_ids[]',
                'label'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Assigned to Website'),
                'title'    => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Assigned to Website'),
                'required' => true,
                'values'   => Mage::getSingleton('Mage_Core_Model_System_Store')->getWebsiteValuesForForm(),
                'value'    => $model->getWebsiteIds()
            ));
        }

        $fieldset->addField('is_active', 'select', array(
            'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Status'),
            'name' => 'is_active',
            'required' => true,
            'options' => array(
                '1' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Active'),
                '0' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Inactive')
            )
        ));

        $applyToFieldConfig = array(
            'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Apply To'),
            'name' => 'apply_to',
            'required' => false,
            'disabled' => (boolean)$model->getId(),
            'options' => array(
                Enterprise_CustomerSegment_Model_Segment::APPLY_TO_VISITORS_AND_REGISTERED => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Visitors and Registered Customers'),
                Enterprise_CustomerSegment_Model_Segment::APPLY_TO_REGISTERED => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Registered Customers'),
                Enterprise_CustomerSegment_Model_Segment::APPLY_TO_VISITORS => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Visitors')
            )
        );
        if (!$model->getId()) {
            $applyToFieldConfig['note'] = Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Please save this information to specify segmentation conditions.');
        }

        $fieldset->addField('apply_to', 'select', $applyToFieldConfig);

        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
