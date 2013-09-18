<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Adminhtml\Rma\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\Adminhtml\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            ))
        );

        $model = $this->_coreRegistry->registry('current_rma');

        if ($model) {
            if ($model->getId()) {
                $form->addField('entity_id', 'hidden', array(
                    'name' => 'entity_id',
                ));
                $form->setValues($model->getData());
            }

            $this->_order = ($model->getOrderId());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
