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
 * Adminhtml newsletter queue edit form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Newsletter\Queue\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Prepare form for newsletter queue editing.
     * Form can be run from newsletter template grid by option "Queue newsletter"
     * or from  newsletter queue grid by edit option.
     *
     * @param void
     * @return \Magento\Adminhtml\Block\Newsletter\Queue\Edit\Form
     */
    protected function _prepareForm()
    {
        /* @var $queue \Magento\Newsletter\Model\Queue */
        $queue = \Mage::getSingleton('Magento\Newsletter\Model\Queue');

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    =>  __('Queue Information'),
            'class'    =>  'fieldset-wide'
        ));

        $dateFormat = \Mage::app()->getLocale()->getDateFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM);
        $timeFormat = \Mage::app()->getLocale()->getTimeFormat(\Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM);

        if ($queue->getQueueStatus() == \Magento\Newsletter\Model\Queue::STATUS_NEVER) {
            $fieldset->addField('date', 'date', array(
                'name'      =>    'start_at',
                'date_format' => $dateFormat,
                'time_format' => $timeFormat,
                'label'     =>    __('Queue Date Start'),
                'image'     =>    $this->getViewFileUrl('images/grid-cal.gif')
            ));

            if (!\Mage::app()->hasSingleStore()) {
                $fieldset->addField('stores', 'multiselect', array(
                    'name'          => 'stores[]',
                    'label'         => __('Subscribers From'),
                    'image'         => $this->getViewFileUrl('images/grid-cal.gif'),
                    'values'        => \Mage::getSingleton('Magento\Core\Model\System\Store')->getStoreValuesForForm(),
                    'value'         => $queue->getStores()
                ));
            }
            else {
                $fieldset->addField('stores', 'hidden', array(
                    'name'      => 'stores[]',
                    'value'     => \Mage::app()->getStore(true)->getId()
                ));
            }
        } else {
            $fieldset->addField('date','date',array(
                'name'      => 'start_at',
                'disabled'  => 'true',
                'style'     => 'width:38%;',
                'date_format' => $dateFormat,
                'time_format' => $timeFormat,
                'label'     => __('Queue Date Start'),
                'image'     => $this->getViewFileUrl('images/grid-cal.gif')
            ));

            if (!\Mage::app()->hasSingleStore()) {
                $fieldset->addField('stores', 'multiselect', array(
                    'name'          => 'stores[]',
                    'label'         => __('Subscribers From'),
                    'image'         => $this->getViewFileUrl('images/grid-cal.gif'),
                    'required'      => true,
                    'values'        => \Mage::getSingleton('Magento\Core\Model\System\Store')->getStoreValuesForForm(),
                    'value'         => $queue->getStores()
                ));
            } else {
                $fieldset->addField('stores', 'hidden', array(
                    'name'      => 'stores[]',
                    'value'     => \Mage::app()->getStore(true)->getId()
                ));
            }
        }

        if ($queue->getQueueStartAt()) {
            $form->getElement('date')->setValue(
                \Mage::app()->getLocale()->date($queue->getQueueStartAt(), \Magento\Date::DATETIME_INTERNAL_FORMAT)
            );
        }

        $fieldset->addField('subject', 'text', array(
            'name'      =>'subject',
            'label'     => __('Subject'),
            'required'  => true,
            'value'     => (
                $queue->isNew() ? $queue->getTemplate()->getTemplateSubject() : $queue->getNewsletterSubject()
            )
        ));

        $fieldset->addField('sender_name', 'text', array(
            'name'      =>'sender_name',
            'label'     => __('Sender Name'),
            'title'     => __('Sender Name'),
            'required'  => true,
            'value'     => (
                $queue->isNew() ? $queue->getTemplate()->getTemplateSenderName() : $queue->getNewsletterSenderName()
            )
        ));

        $fieldset->addField('sender_email', 'text', array(
            'name'      =>'sender_email',
            'label'     => __('Sender Email'),
            'title'     => __('Sender Email'),
            'class'     => 'validate-email',
            'required'  => true,
            'value'     => (
                $queue->isNew() ? $queue->getTemplate()->getTemplateSenderEmail() : $queue->getNewsletterSenderEmail()
            )
        ));

        $widgetFilters = array('is_email_compatible' => 1);
        $wysiwygConfig = \Mage::getSingleton('Magento\Cms\Model\Wysiwyg\Config')
            ->getConfig(array('widget_filters' => $widgetFilters));

        if ($queue->isNew()) {
            $fieldset->addField('text', 'editor', array(
                'name'      => 'text',
                'label'     => __('Message'),
                'state'     => 'html',
                'required'  => true,
                'value'     => $queue->getTemplate()->getTemplateText(),
                'style'     => 'height: 600px;',
                'config'    => $wysiwygConfig
            ));

            $fieldset->addField('styles', 'textarea', array(
                'name'          =>'styles',
                'label'         => __('Newsletter Styles'),
                'container_id'  => 'field_newsletter_styles',
                'value'         => $queue->getTemplate()->getTemplateStyles()
            ));
        } elseif (\Magento\Newsletter\Model\Queue::STATUS_NEVER != $queue->getQueueStatus()) {
            $fieldset->addField('text', 'textarea', array(
                'name'      =>    'text',
                'label'     =>    __('Message'),
                'value'     =>    $queue->getNewsletterText(),
            ));

            $fieldset->addField('styles', 'textarea', array(
                'name'          =>'styles',
                'label'         => __('Newsletter Styles'),
                'value'         => $queue->getNewsletterStyles()
            ));

            $form->getElement('text')->setDisabled('true')->setRequired(false);
            $form->getElement('styles')->setDisabled('true')->setRequired(false);
            $form->getElement('subject')->setDisabled('true')->setRequired(false);
            $form->getElement('sender_name')->setDisabled('true')->setRequired(false);
            $form->getElement('sender_email')->setDisabled('true')->setRequired(false);
            $form->getElement('stores')->setDisabled('true');
        } else {
            $fieldset->addField('text', 'editor', array(
                'name'      =>    'text',
                'label'     =>    __('Message'),
                'state'     => 'html',
                'required'  => true,
                'value'     =>    $queue->getNewsletterText(),
                'style'     => 'height: 600px;',
                'config'    => $wysiwygConfig
            ));

            $fieldset->addField('styles', 'textarea', array(
                'name'          =>'styles',
                'label'         => __('Newsletter Styles'),
                'value'         => $queue->getNewsletterStyles(),
                'style'         => 'height: 300px;',
            ));
        }

        $this->setForm($form);
        return $this;
    }
}
