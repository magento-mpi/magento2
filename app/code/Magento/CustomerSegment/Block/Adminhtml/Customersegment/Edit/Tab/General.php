<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * General Properties tab of customer segment configuration
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_General
    extends Magento_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare general properties form
     *
     * @return Magento_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_General
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('current_customer_segment');

        $form = $this->_createForm();

        $form->setHtmlIdPrefix('segment_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('General Properties')
        ));

        if ($model->getId()) {
            $fieldset->addField('segment_id', 'hidden', array(
                'name' => 'segment_id'
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => __('Segment Name'),
            'required' => true
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => __('Description'),
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
                'label'    => __('Assigned to Website'),
                'title'    => __('Assigned to Website'),
                'required' => true,
                'values'   => Mage::getSingleton('Magento_Core_Model_System_Store')->getWebsiteValuesForForm(),
                'value'    => $model->getWebsiteIds()
            ));
        }

        $fieldset->addField('is_active', 'select', array(
            'label' => __('Status'),
            'name' => 'is_active',
            'required' => true,
            'options' => array(
                '1' => __('Active'),
                '0' => __('Inactive')
            )
        ));

        $applyToFieldConfig = array(
            'label' => __('Apply To'),
            'name' => 'apply_to',
            'required' => false,
            'disabled' => (boolean)$model->getId(),
            'options' => array(
                Magento_CustomerSegment_Model_Segment::APPLY_TO_VISITORS_AND_REGISTERED => __('Visitors and Registered Customers'),
                Magento_CustomerSegment_Model_Segment::APPLY_TO_REGISTERED => __('Registered Customers'),
                Magento_CustomerSegment_Model_Segment::APPLY_TO_VISITORS => __('Visitors')
            )
        );
        if (!$model->getId()) {
            $applyToFieldConfig['note'] = __('Please save this information to specify segmentation conditions.');
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
