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
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Cms\Page\Edit\Tab;

class Content
    extends \Magento\Adminhtml\Block\Widget\Form
    implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Event\Manager $eventManager,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($context, $data);
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
        /** @var $model \Magento\Cms\Model\Page */
        $model = \Mage::registry('cms_page');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Magento_Cms::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }


        $form = new \Magento\Data\Form();

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
