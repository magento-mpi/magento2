<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Adminhtml\Block\System\Design\Edit\Tab;

class General extends \Magento\Adminhtml\Block\Widget\Form
{

    /**
     * Initialise form fields
     *
     * @return \Magento\Adminhtml\Block\System\Design\Edit\Tab\General
     */
    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form();

        $fieldset = $form->addFieldset('general', array(
            'legend' => __('General Settings'))
        );

        if (!\Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'label'    => __('Store'),
                'title'    => __('Store'),
                'values'   => \Mage::getSingleton('Magento\Core\Model\System\Store')->getStoreValuesForForm(),
                'name'     => 'store_id',
                'required' => true,
            ));
            $renderer = $this->getLayout()
                ->createBlock('\Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id',
                'value'     => \Mage::app()->getStore(true)->getId(),
            ));
        }

        /** @var $label \Magento\Core\Model\Theme\Label */
        $label = \Mage::getModel('\Magento\Core\Model\Theme\Label');
        $options = $label->getLabelsCollection(__('-- Please Select --'));
        $fieldset->addField('design', 'select', array(
            'label'    => __('Custom Design'),
            'title'    => __('Custom Design'),
            'values'   => $options,
            'name'     => 'design',
            'required' => true,
        ));

        $dateFormat = \Mage::app()->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_SHORT);
        $fieldset->addField('date_from', 'date', array(
            'label'    => __('Date From'),
            'title'    => __('Date From'),
            'name'     => 'date_from',
            'image'    => $this->getViewFileUrl('images/grid-cal.gif'),
            'date_format' => $dateFormat,
            //'required' => true,
        ));
        $fieldset->addField('date_to', 'date', array(
            'label'    => __('Date To'),
            'title'    => __('Date To'),
            'name'     => 'date_to',
            'image'    => $this->getViewFileUrl('images/grid-cal.gif'),
            'date_format' => $dateFormat,
            //'required' => true,
        ));

        $formData = \Mage::getSingleton('Magento\Adminhtml\Model\Session')->getDesignData(true);
        if (!$formData) {
            $formData = \Mage::registry('design')->getData();
        } else {
            $formData = $formData['design'];
        }

        $form->addValues($formData);
        $form->setFieldNameSuffix('design');
        $this->setForm($form);
    }

}
