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
 * Rating edit form block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Rating\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form   = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
            ))
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
