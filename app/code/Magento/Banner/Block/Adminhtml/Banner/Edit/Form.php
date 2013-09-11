<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Block\Adminhtml\Banner\Edit;

class Form extends \Magento\Adminhtml\Block\Widget\Form
{

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\Adminhtml\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );

        $banner = \Mage::registry('current_banner');

        if ($banner->getId()) {
            $form->addField('banner_id', 'hidden', array(
                'name' => 'banner_id',
            ));
            $form->setValues($banner->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
