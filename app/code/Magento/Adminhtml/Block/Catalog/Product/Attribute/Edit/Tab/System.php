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
 * Product attribute add/edit form system tab
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Catalog\Product\Attribute\Edit\Tab;

class System extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('entity_attribute');

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('System Properties')));

        if ($model->getAttributeId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $yesno = array(
            array(
                'value' => 0,
                'label' => __('No')
            ),
            array(
                'value' => 1,
                'label' => __('Yes')
            ));

        $fieldset->addField('backend_type', 'select', array(
            'name' => 'backend_type',
            'label' => __('Data Type for Saving in Database'),
            'title' => __('Data Type for Saving in Database'),
            'options' => array(
                'text'      => __('Text'),
                'varchar'   => __('Varchar'),
                'static'    => __('Static'),
                'datetime'  => __('Datetime'),
                'decimal'   => __('Decimal'),
                'int'       => __('Integer'),
            ),
        ));

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => __('Globally Editable'),
            'title' => __('Globally Editable'),
            'values'=> $yesno,
        ));

        $form->setValues($model->getData());

        if ($model->getAttributeId()) {
            $form->getElement('backend_type')->setDisabled(1);
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
