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
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Catalog\Product\Attribute\Set\Main;

class Formattribute extends \Magento\Adminhtml\Block\Widget\Form\Generic
{
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('set_fieldset', array('legend'=>__('Add New Attribute')));

        $fieldset->addField('new_attribute', 'text',
                            array(
                                'label' => __('Name'),
                                'name' => 'new_attribute',
                                'required' => true,
                            )
        );

        $fieldset->addField('submit', 'note',
                            array(
                                'text' => $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')
                                            ->setData(array(
                                                'label'     => __('Add Attribute'),
                                                'onclick'   => 'this.form.submit();',
                                                                                                'class' => 'add'
                                            ))
                                            ->toHtml(),
                            )
        );

        $form->setUseContainer(true);
        $form->setMethod('post');
        $this->setForm($form);
    }
}
