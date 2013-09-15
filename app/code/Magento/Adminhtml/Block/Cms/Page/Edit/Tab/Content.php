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
 * Cms page edit form main tab
 */

namespace Magento\Adminhtml\Block\Cms\Page\Edit\Tab;

class Content
    extends \Magento\Backend\Block\Widget\Form\Generic
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        \Magento\Core\Model\Event\Manager $eventManager,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($coreRegistry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (\Mage::getSingleton('Magento\Cms\Model\Wysiwyg\Config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    protected function _prepareForm()
    {
        /** @var $model Magento_Cms_Model_Page */
        $model = $this->_coreRegistry->registry('cms_page');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Magento_Cms::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }


        /** @var Magento_Data_Form $form */
        $form   = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('content_fieldset', array('legend'=>__('Content'),'class'=>'fieldset-wide'));

        $wysiwygConfig = \Mage::getSingleton('Magento\Cms\Model\Wysiwyg\Config')->getConfig(
            array('tab_id' => $this->getTabId())
        );

        $fieldset->addField('content_heading', 'text', array(
            'name'      => 'content_heading',
            'label'     => __('Content Heading'),
            'title'     => __('Content Heading'),
            'disabled'  => $isElementDisabled
        ));

        $contentField = $fieldset->addField('content', 'editor', array(
            'name'      => 'content',
            'style'     => 'height:36em;',
            'required'  => true,
            'disabled'  => $isElementDisabled,
            'config'    => $wysiwygConfig
        ));

        // Setting custom renderer for content field to remove label column
        $renderer = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Form\Renderer\Fieldset\Element')
                    ->setTemplate('cms/page/edit/form/renderer/content.phtml');
        $contentField->setRenderer($renderer);

        $this->_eventManager->dispatch('adminhtml_cms_page_edit_tab_content_prepare_form', array('form' => $form));
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Content');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Content');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
