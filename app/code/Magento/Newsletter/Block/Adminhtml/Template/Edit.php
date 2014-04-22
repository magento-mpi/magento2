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
 * Newsletter Template Edit Block
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Block\Adminhtml\Template;

class Edit extends \Magento\Backend\Block\Widget
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve template object
     *
     * @return \Magento\Newsletter\Model\Template
     */
    public function getModel()
    {
        return $this->_coreRegistry->registry('_current_template');
    }

    /**
     * Preparing block layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        // Load Wysiwyg on demand and Prepare layout
        $block = $this->getLayout()->getBlock('head');
        if ($this->_wysiwygConfig->isEnabled() && $block) {
            $block->setCanLoadTinyMce(true);
        }

        $this->getToolbar()->addChild(
            'back_button',
            'Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('Back'),
                'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                'class' => 'action-back'
            )
        );

        $this->getToolbar()->addChild(
            'reset_button',
            'Magento\Backend\Block\Widget\Button',
            array(
                'label' => __('Reset'),
                'onclick' => 'window.location.href = window.location.href',
                'class' => 'reset'
            )
        );

        if (!$this->isTextType()) {
            $this->getToolbar()->addChild(
                'to_plain_button',
                'Magento\Backend\Block\Widget\Button',
                array(
                    'label' => __('Convert to Plain Text'),
                    'onclick' => 'templateControl.stripTags();',
                    'id' => 'convert_button',
                    'class' => 'convert'
                )
            );

            $this->getToolbar()->addChild(
                'to_html_button',
                'Magento\Backend\Block\Widget\Button',
                array(
                    'label' => __('Return HTML Version'),
                    'onclick' => 'templateControl.unStripTags();',
                    'id' => 'convert_button_back',
                    'style' => 'display:none',
                    'class' => 'return'
                )
            );
        }

        $this->getToolbar()->addChild(
            'preview_button',
            'Magento\Backend\Block\Widget\Button',
            array('label' => __('Preview Template'), 'onclick' => 'templateControl.preview();', 'class' => 'preview')
        );

        if ($this->getEditMode()) {
            $this->getToolbar()->addChild(
                'delete_button',
                'Magento\Backend\Block\Widget\Button',
                array(
                    'label' => __('Delete Template'),
                    'onclick' => 'templateControl.deleteTemplate();',
                    'class' => 'delete'
                )
            );

            $this->getToolbar()->addChild(
                'save_as_button',
                'Magento\Backend\Block\Widget\Button',
                array('label' => __('Save As'), 'onclick' => 'templateControl.saveAs();', 'class' => 'save-as')
            );
        }

        $this->getToolbar()->addChild(
            'save_button',
            'Magento\Backend\Block\Widget\Button',
            array('label' => __('Save Template'), 'onclick' => 'templateControl.save();', 'class' => 'save primary')
        );

        return parent::_prepareLayout();
    }

    /**
     * Return edit flag for block
     *
     * @return boolean
     */
    public function getEditMode()
    {
        if ($this->getModel()->getId()) {
            return true;
        }
        return false;
    }

    /**
     * Return header text for form
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getEditMode()) {
            return __('Edit Newsletter Template');
        }

        return __('New Newsletter Template');
    }

    /**
     * Return form block HTML
     *
     * @return string
     */
    public function getForm()
    {
        return $this->getLayout()->createBlock('Magento\Newsletter\Block\Adminhtml\Template\Edit\Form')->toHtml();
    }

    /**
     * Return return template name for JS
     *
     * @return string
     */
    public function getJsTemplateName()
    {
        return addcslashes($this->getModel()->getTemplateCode(), "\"\r\n\\");
    }

    /**
     * Return action url for form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save');
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
     * Check Template Type is Plain Text
     *
     * @return bool
     */
    public function isTextType()
    {
        return $this->getModel()->isPlain();
    }

    /**
     * Return delete url for customer group
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('id' => $this->getRequest()->getParam('id')));
    }

    /**
     * Retrieve Save As Flag
     *
     * @return int
     */
    public function getSaveAsFlag()
    {
        return $this->getRequest()->getParam('_save_as_flag') ? '1' : '';
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
}
