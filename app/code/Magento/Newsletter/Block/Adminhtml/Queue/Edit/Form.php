<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Block\Adminhtml\Queue\Edit;

/**
 * Newsletter queue edit form
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Newsletter\Model\QueueFactory
     */
    protected $_queueFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Newsletter\Model\QueueFactory $queueFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Newsletter\Model\QueueFactory $queueFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = array()
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
        $this->_queueFactory = $queueFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form for newsletter queue editing.
     * Form can be run from newsletter template grid by option "Queue newsletter"
     * or from  newsletter queue grid by edit option.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $queue \Magento\Newsletter\Model\Queue */
        $queue = $this->getQueue();

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            array('legend' => __('Queue Information'), 'class' => 'fieldset-wide')
        );

        $dateFormat = $this->_localeDate->getDateFormat(
            \Magento\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM
        );
        $timeFormat = $this->_localeDate->getTimeFormat(
            \Magento\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM
        );

        if ($queue->getQueueStatus() == \Magento\Newsletter\Model\Queue::STATUS_NEVER) {
            $fieldset->addField(
                'date',
                'date',
                array(
                    'name' => 'start_at',
                    'date_format' => $dateFormat,
                    'time_format' => $timeFormat,
                    'label' => __('Queue Date Start'),
                    'image' => $this->getViewFileUrl('images/grid-cal.gif')
                )
            );

            if (!$this->_storeManager->hasSingleStore()) {
                $fieldset->addField(
                    'stores',
                    'multiselect',
                    array(
                        'name' => 'stores[]',
                        'label' => __('Subscribers From'),
                        'image' => $this->getViewFileUrl('images/grid-cal.gif'),
                        'values' => $this->_systemStore->getStoreValuesForForm(),
                        'value' => $queue->getStores()
                    )
                );
            } else {
                $fieldset->addField(
                    'stores',
                    'hidden',
                    array('name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId())
                );
            }
        } else {
            $fieldset->addField(
                'date',
                'date',
                array(
                    'name' => 'start_at',
                    'disabled' => 'true',
                    'style' => 'width:38%;',
                    'date_format' => $dateFormat,
                    'time_format' => $timeFormat,
                    'label' => __('Queue Date Start'),
                    'image' => $this->getViewFileUrl('images/grid-cal.gif')
                )
            );

            if (!$this->_storeManager->hasSingleStore()) {
                $fieldset->addField(
                    'stores',
                    'multiselect',
                    array(
                        'name' => 'stores[]',
                        'label' => __('Subscribers From'),
                        'image' => $this->getViewFileUrl('images/grid-cal.gif'),
                        'required' => true,
                        'values' => $this->_systemStore->getStoreValuesForForm(),
                        'value' => $queue->getStores()
                    )
                );
            } else {
                $fieldset->addField(
                    'stores',
                    'hidden',
                    array('name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId())
                );
            }
        }

        if ($queue->getQueueStartAt()) {
            $form->getElement(
                'date'
            )->setValue(
                $this->_localeDate->date($queue->getQueueStartAt(), \Magento\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT)
            );
        }

        $fieldset->addField(
            'subject',
            'text',
            array(
                'name' => 'subject',
                'label' => __('Subject'),
                'required' => true,
                'value' => $queue->isNew() ? $queue
                    ->getTemplate()
                    ->getTemplateSubject() : $queue
                    ->getNewsletterSubject()
            )
        );

        $fieldset->addField(
            'sender_name',
            'text',
            array(
                'name' => 'sender_name',
                'label' => __('Sender Name'),
                'title' => __('Sender Name'),
                'required' => true,
                'value' => $queue->isNew() ? $queue
                    ->getTemplate()
                    ->getTemplateSenderName() : $queue
                    ->getNewsletterSenderName()
            )
        );

        $fieldset->addField(
            'sender_email',
            'text',
            array(
                'name' => 'sender_email',
                'label' => __('Sender Email'),
                'title' => __('Sender Email'),
                'class' => 'validate-email',
                'required' => true,
                'value' => $queue->isNew() ? $queue
                    ->getTemplate()
                    ->getTemplateSenderEmail() : $queue
                    ->getNewsletterSenderEmail()
            )
        );

        $widgetFilters = array('is_email_compatible' => 1);
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(array('widget_filters' => $widgetFilters));

        if ($queue->isNew()) {
            $fieldset->addField(
                'text',
                'editor',
                array(
                    'name' => 'text',
                    'label' => __('Message'),
                    'state' => 'html',
                    'required' => true,
                    'value' => $queue->getTemplate()->getTemplateText(),
                    'style' => 'height: 600px;',
                    'config' => $wysiwygConfig
                )
            );

            $fieldset->addField(
                'styles',
                'textarea',
                array(
                    'name' => 'styles',
                    'label' => __('Newsletter Styles'),
                    'container_id' => 'field_newsletter_styles',
                    'value' => $queue->getTemplate()->getTemplateStyles()
                )
            );
        } elseif (\Magento\Newsletter\Model\Queue::STATUS_NEVER != $queue->getQueueStatus()) {
            $fieldset->addField(
                'text',
                'textarea',
                array('name' => 'text', 'label' => __('Message'), 'value' => $queue->getNewsletterText())
            );

            $fieldset->addField(
                'styles',
                'textarea',
                array('name' => 'styles', 'label' => __('Newsletter Styles'), 'value' => $queue->getNewsletterStyles())
            );

            $form->getElement('text')->setDisabled('true')->setRequired(false);
            $form->getElement('styles')->setDisabled('true')->setRequired(false);
            $form->getElement('subject')->setDisabled('true')->setRequired(false);
            $form->getElement('sender_name')->setDisabled('true')->setRequired(false);
            $form->getElement('sender_email')->setDisabled('true')->setRequired(false);
            $form->getElement('stores')->setDisabled('true');
        } else {
            $fieldset->addField(
                'text',
                'editor',
                array(
                    'name' => 'text',
                    'label' => __('Message'),
                    'state' => 'html',
                    'required' => true,
                    'value' => $queue->getNewsletterText(),
                    'style' => 'height: 600px;',
                    'config' => $wysiwygConfig
                )
            );

            $fieldset->addField(
                'styles',
                'textarea',
                array(
                    'name' => 'styles',
                    'label' => __('Newsletter Styles'),
                    'value' => $queue->getNewsletterStyles(),
                    'style' => 'height: 300px;'
                )
            );
        }

        $this->setForm($form);
        return $this;
    }

    /**
     * Retrieve queue object
     *
     * @return \Magento\Newsletter\Model\Queue
     */
    protected function getQueue()
    {
        $queue = $this->_coreRegistry->registry('current_queue');
        if (!$queue) {
            $queue = $this->_queueFactory->create();
        }
        return $queue;
    }
}
