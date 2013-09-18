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
 * Adminhtml customer groups edit form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Customer\Group\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form for render
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();

        $customerGroup = $this->_coreRegistry->registry('current_group');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Group Information')));

        $validateClass = sprintf('required-entry validate-length maximum-length-%d',
            \Magento\Customer\Model\Group::GROUP_CODE_MAX_LENGTH);
        $name = $fieldset->addField('customer_group_code', 'text',
            array(
                'name'  => 'code',
                'label' => __('Group Name'),
                'title' => __('Group Name'),
                'note'  => __('Maximum length must be less then %1 symbols',
                    \Magento\Customer\Model\Group::GROUP_CODE_MAX_LENGTH),
                'class' => $validateClass,
                'required' => true,
            )
        );

        if ($customerGroup->getId()==0 && $customerGroup->getCustomerGroupCode() ) {
            $name->setDisabled(true);
        }

        $fieldset->addField('tax_class_id', 'select',
            array(
                'name'  => 'tax_class',
                'label' => __('Tax Class'),
                'title' => __('Tax Class'),
                'class' => 'required-entry',
                'required' => true,
                'values' => \Mage::getSingleton('Magento\Tax\Model\TaxClass\Source\Customer')->toOptionArray()
            )
        );

        if (!is_null($customerGroup->getId())) {
            // If edit add id
            $form->addField('id', 'hidden',
                array(
                    'name'  => 'id',
                    'value' => $customerGroup->getId(),
                )
            );
        }

        if ( \Mage::getSingleton('Magento\Adminhtml\Model\Session')->getCustomerGroupData() ) {
            $form->addValues(\Mage::getSingleton('Magento\Adminhtml\Model\Session')->getCustomerGroupData());
            \Mage::getSingleton('Magento\Adminhtml\Model\Session')->setCustomerGroupData(null);
        } else {
            $form->addValues($customerGroup->getData());
        }

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $this->setForm($form);
    }
}
