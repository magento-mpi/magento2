<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme Edit Form
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
namespace Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Initialize theme form
     *
     * @return \Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form|\Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'      => 'edit_form',
                'action'  => $this->getUrl('*/*/save'),
                'enctype' => 'multipart/form-data',
                'method'  => 'post',
            ))
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
