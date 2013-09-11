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
 * Textarea attribute WYSIWYG content
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Helper\Form\Wysiwyg;

class Content
    extends \Magento\Adminhtml\Block\Widget\Form
{
    /**
     * Prepare form.
     * Adding editor field to render
     *
     * @return \Magento\Adminhtml\Block\Catalog\Helper\Form\Wysiwyg\Content
     */
    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form(array('id' => 'wysiwyg_edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $config['document_base_url']     = $this->getData('store_media_url');
        $config['store_id']              = $this->getData('store_id');
        $config['add_variables']         = false;
        $config['add_widgets']           = false;
        $config['add_directives']        = true;
        $config['use_container']         = true;
        $config['container_class']       = 'hor-scroll';

        $form->addField($this->getData('editor_element_id'), 'editor', array(
            'name'      => 'content',
            'style'     => 'width:725px;height:460px',
            'required'  => true,
            'force_load' => true,
            'config'    => \Mage::getSingleton('Magento\Cms\Model\Wysiwyg\Config')->getConfig($config)
        ));
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
