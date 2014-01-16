<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter queue edit block
 */
namespace Magento\Newsletter\Block\Adminhtml\Queue;

class Edit extends \Magento\Backend\Block\Template
{
    protected $_template = 'queue/edit.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $templateId = $this->getRequest()->getParam('template_id');
        if ($templateId) {
            $this->setTemplateId($templateId);
        }
    }

    /**
     * Retrieve current Newsletter Queue Object
     *
     * @return \Magento\Newsletter\Model\Queue
     */
    public function getQueue()
    {
        return $this->_coreRegistry->registry('current_queue');
    }

    protected  function _beforeToHtml()
    {
        $this->setChild('form',
            $this->getLayout()->createBlock('Magento\Newsletter\Block\Adminhtml\Queue\Edit\Form','form')
        );
        return parent::_beforeToHtml();
    }

    public function getSaveUrl()
    {
        if ($this->getTemplateId()) {
            $params = array('template_id' => $this->getTemplateId());
        } else {
            $params = array('id' => $this->getRequest()->getParam('id'));
        }
        return $this->getUrl('*/*/save', $params);
    }

    protected function _prepareLayout()
    {
        // Load Wysiwyg on demand and Prepare layout
        if ($this->_wysiwygConfig->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }

        $this->addChild('preview_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Preview Template'),
            'onclick'   => 'queueControl.preview();',
            'class'     => 'preview'
        ));

        $this->addChild('save_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Save Newsletter'),
            'class'     => 'save primary',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '#queue_edit_form'),
                ),
            ),
        ));

        $this->addChild('save_and_resume', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Save and Resume'),
            'class'     => 'save',
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array(
                        'event' => 'save',
                        'target' => '#queue_edit_form',
                        'eventData' => array(
                            'action' => array(
                                'args' => array('_resume' => 1),
                            ),
                        ),
                    ),
                ),
            ),
        ));

        $this->addChild('reset_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Reset'),
            'onclick'   => 'window.location = window.location'
        ));

        $this->addChild('back_button','Magento\Backend\Block\Widget\Button', array(
            'label'   => __('Back'),
            'onclick' => "window.location.href = '" . $this->getUrl((
                $this->getTemplateId() ? '*/template' : '*/*')) . "'",
            'class'   => 'action-back'
        ));

        return parent::_prepareLayout();
    }

    /**
     * Return preview action url for form
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/preview');
    }

    /**
     * Retrieve Preview Button HTML
     *
     * @return string
     */
    public function getPreviewButtonHtml()
    {
        return $this->getChildHtml('preview_button');
    }

    /**
     * Retrieve Save Button HTML
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Retrieve Reset Button HTML
     *
     * @return string
     */
    public function getResetButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Retrieve Back Button HTML
     *
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * Retrieve Resume Button HTML
     *
     * @return string
     */
    public function getResumeButtonHtml()
    {
        return $this->getChildHtml('save_and_resume');
    }

    /**
     * Getter for availability preview mode
     *
     * @return boolean
     */
    public function getIsPreview()
    {
        return !in_array($this->getQueue()->getQueueStatus(), array(
            \Magento\Newsletter\Model\Queue::STATUS_NEVER,
            \Magento\Newsletter\Model\Queue::STATUS_PAUSE
        ));
    }

    /**
     * Getter for single store mode check
     *
     * @return boolean
     */
    protected function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }

    /**
     * Getter for id of current store (the only one in single-store mode and current in multi-stores mode)
     *
     * @return boolean
     */
    protected function getStoreId()
    {
        return $this->_storeManager->getStore(true)->getId();
    }

    /**
     * Getter for check is this newsletter the plain text.
     *
     * @return boolean
     */
    public function getIsTextType()
    {
        return $this->getQueue()->isPlain();
    }

    /**
     * Getter for availability resume action
     *
     * @return boolean
     */
    public function getCanResume()
    {
        return in_array($this->getQueue()->getQueueStatus(), array(
            \Magento\Newsletter\Model\Queue::STATUS_PAUSE
        ));
    }

    /**
     * Getter for header text
     *
     * @return boolean
     */
    public function getHeaderText()
    {
        return ( $this->getIsPreview() ? __('View Newsletter') : __('Edit Newsletter'));
    }
}
