<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Attributes Edit container
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Attribute\Edit;

class Form
    extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Attribute\Edit\Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'        => 'edit_form',
                'action'    => $this->getData('action'),
                'method'    => 'post',
            ))
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
