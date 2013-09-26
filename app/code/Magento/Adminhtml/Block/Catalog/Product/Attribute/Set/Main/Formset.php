<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\Catalog\Product\Attribute\Set\Main;

class Formset
    extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepares attribute set form
     *
     */
    protected function _prepareForm()
    {
        $data = \Mage::getModel('Magento\Eav\Model\Entity\Attribute\Set')
            ->load($this->getRequest()->getParam('id'));

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset('set_name', array('legend'=> __('Edit Set Name')));
        $fieldset->addField('attribute_set_name', 'text', array(
            'label' => __('Name'),
            'note' => __('For internal use'),
            'name' => 'attribute_set_name',
            'required' => true,
            'class' => 'required-entry validate-no-html-tags',
            'value' => $data->getAttributeSetName()
        ));

        if( !$this->getRequest()->getParam('id', false) ) {
            $fieldset->addField('gotoEdit', 'hidden', array(
                'name' => 'gotoEdit',
                'value' => '1'
            ));

            $sets = \Mage::getModel('Magento\Eav\Model\Entity\Attribute\Set')
                ->getResourceCollection()
                ->setEntityTypeFilter($this->_coreRegistry->registry('entityType'))
                ->load()
                ->toOptionArray();

            $fieldset->addField('skeleton_set', 'select', array(
                'label' => __('Based On'),
                'name' => 'skeleton_set',
                'required' => true,
                'class' => 'required-entry',
                'values' => $sets,
            ));
        }

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('set-prop-form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setOnsubmit('return false;');
        $this->setForm($form);
    }
}
