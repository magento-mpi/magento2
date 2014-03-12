<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rating\Block\Adminhtml\Edit;

/**
 * Rating edit form block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form   = $this->_formFactory->create(array(
            'data' => array(
                'id' => 'edit_form',
                'action' => $this->getUrl('rating/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
            ))
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
