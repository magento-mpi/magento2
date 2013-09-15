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
 * Adminhtml cms page edit form block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Cms\Page\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form   = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getData('action'),
                'method' => 'post',
            ))
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
