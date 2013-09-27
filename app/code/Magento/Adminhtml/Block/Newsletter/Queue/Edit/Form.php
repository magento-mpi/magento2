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

class Magento_Adminhtml_Block_Newsletter_Queue_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * @var Magento_Cms_Model_Wysiwyg_Config
     */
    protected $_wysiwygConfig;

    /**
     * @var Magento_Core_Model_System_Store
     */
    protected $_systemStore;

    /**
     * @var Magento_Newsletter_Model_QueueFactory
     */
    protected $_queueFactory;

    /**
     * @param Magento_Newsletter_Model_QueueFactory $queueFactory
     * @param Magento_Core_Model_System_Store $systemStore
     * @param Magento_Cms_Model_Wysiwyg_Config $wysiwygConfig
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Newsletter_Model_QueueFactory $queueFactory,
        Magento_Core_Model_System_Store $systemStore,
        Magento_Cms_Model_Wysiwyg_Config $wysiwygConfig,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
        $this->_queueFactory = $queueFactory;
        parent::__construct($registry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Prepare form for newsletter queue editing.
     * Form can be run from newsletter template grid by option "Queue newsletter"
     * or from  newsletter queue grid by edit option.
     *
     * @param void
     * @return Magento_Adminhtml_Block_Newsletter_Queue_Edit_Form
     */
    protected function _prepareForm()
    {
        /* @var $queue Magento_Newsletter_Model_Queue */
        $queue = $this->_queueFactory->create();

        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    =>  __('Queue Information'),
            'class'    =>  'fieldset-wide'
        ));

        $dateFormat = $this->_locale->getDateFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);
        $timeFormat = $this->_locale->getTimeFormat(Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM);

        if ($queue->getQueueStatus() == Magento_Newsletter_Model_Queue::STATUS_NEVER) {
            $fieldset->addField('date', 'date', array(
                'name'      =>    'start_at',
                'date_format' => $dateFormat,
                'time_format' => $timeFormat,
                'label'     =>    __('Queue Date Start'),
                'image'     =>    $this->getViewFileUrl('images/grid-cal.gif')
            ));

            if (!$this->_storeManager->hasSingleStore()) {
                $fieldset->addField('stores', 'multiselect', array(
                    'name'          => 'stores[]',
                    'label'         => __('Subscribers From'),
                    'image'         => $this->getViewFileUrl('images/grid-cal.gif'),
                    'values'        => $this->_systemStore->getStoreValuesForForm(),
                    'value'         => $queue->getStores()
                ));
            } else {
                $fieldset->addField('stores', 'hidden', array(
                    'name'      => 'stores[]',
                    'value'     => $this->_storeManager->getStore(true)->getId()
                ));
            }
        } else {
            $fieldset->addField('date', 'date', array(
                'name'      => 'start_at',
                'disabled'  => 'true',
                'style'     => 'width:38%;',
                'date_format' => $dateFormat,
                'time_format' => $timeFormat,
                'label'     => __('Queue Date Start'),
                'image'     => $this->getViewFileUrl('images/grid-cal.gif')
            ));

            if (!$this->_storeManager->hasSingleStore()) {
                $fieldset->addField('stores', 'multiselect', array(
                    'name'          => 'stores[]',
                    'label'         => __('Subscribers From'),
                    'image'         => $this->getViewFileUrl('images/grid-cal.gif'),
                    'required'      => true,
                    'values'        => $this->_systemStore->getStoreValuesForForm(),
                    'value'         => $queue->getStores()
                ));
            } else {
                $fieldset->addField('stores', 'hidden', array(
                    'name'      => 'stores[]',
                    'value'     => $this->_storeManager->getStore(true)->getId()
                ));
            }
        }

        if ($queue->getQueueStartAt()) {
            $form->getElement('date')->setValue(
                $this->_locale->date($queue->getQueueStartAt(), Magento_Date::DATETIME_INTERNAL_FORMAT)
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
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(array('widget_filters' => $widgetFilters));

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
        } elseif (Magento_Newsletter_Model_Queue::STATUS_NEVER != $queue->getQueueStatus()) {
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
