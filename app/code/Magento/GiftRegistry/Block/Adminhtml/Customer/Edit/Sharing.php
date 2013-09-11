<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Block\Adminhtml\Customer\Edit;

class Sharing
    extends \Magento\Adminhtml\Block\Widget\Form
{

    protected function _prepareForm()
    {
        $form = new \Magento\Data\Form(array(
            'id' => 'edit_form',
            'action' => $this->getActionUrl(),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('Sharing Information'),
            'class'  => 'fieldset-wide'
        ));

        $fieldset->addField('emails', 'text', array(
            'label'    => __('Emails'),
            'required' => true,
            'class'    => 'validate-emails',
            'name'     => 'emails',
            'note'     => 'Enter list of emails, comma-separated.'
        ));

        if (!\Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'select', array(
                'label'    => __('Send From'),
                'required' => true,
                'name'     => 'store_id',
                'values'   => \Mage::getSingleton('Magento\Core\Model\System\Store')->getStoreValuesForForm()
            ));
        }

        $fieldset->addField('message', 'textarea', array(
            'label' => __('Message'),
            'name'  => 'message',
            'style' => 'height: 50px;',
            'after_element_html' => $this->getShareButton()
        ));

        $form->setUseContainer(true);
        $this->setForm($form);
        $form->setDataObject();

        return parent::_prepareForm();
    }

    /**
     * Return sharing form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/share', array('_current' => true));
    }

    /**
     * Create button
     *
     * @return string
     */
    public function getShareButton()
    {
        return $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')
            ->addData(array(
                'id'      => '',
                'label'   => __('Share Gift Registry'),
                'type'    => 'submit'
            ))->toHtml();
    }
}
