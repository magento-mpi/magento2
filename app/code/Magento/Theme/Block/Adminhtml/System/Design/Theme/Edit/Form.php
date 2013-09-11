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
 */
namespace Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit;

class Form extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Initialize theme form
     *
     * @return \Magento\Theme\Block\Adminhtml\System\Design\Theme\Edit\Form|\Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form(array(
              'id'      => 'edit_form',
              'action'  => $this->getUrl('*/*/save'),
              'enctype' => 'multipart/form-data',
              'method'  => 'post'
         ));

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
