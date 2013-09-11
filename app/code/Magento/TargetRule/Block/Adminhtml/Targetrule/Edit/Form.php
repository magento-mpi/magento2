<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Block\Adminhtml\Targetrule\Edit;

class Form extends \Magento\Adminhtml\Block\Widget\Form
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('magento_targetrule_form');
        $this->setTitle(__('Rule Information'));
    }

    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form(array('id' => 'edit_form',
            'action' => \Mage::helper('Magento\Adminhtml\Helper\Data')->getUrl('*/*/save'), 'method' => 'post'));
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }


}
