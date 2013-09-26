<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cms page edit form revisions tab
 */
class Magento_VersionsCms_Block_Adminhtml_Cms_Page_Edit
    extends Magento_Adminhtml_Block_Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * @var Magento_VersionsCms_Model_Config
     */
    protected $_cmsConfig;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_VersionsCms_Model_Config $cmsConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_VersionsCms_Model_Config $cmsConfig,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Adding js to CE blocks to implement special functionality which
     * will allow go back to edit page with pre loaded tab passed through query string.
     * Added permission checking to remove some buttons if needed.
     *
     * @return Magento_VersionsCms_Block_Adminhtml_Cms_Page_Edit
     */
    protected function _prepareLayout()
    {
        $tabsBlock = $this->getLayout()->getBlock('cms_page_edit_tabs');
        /* @var $tabBlock Magento_Adminhtml_Block_Cms_Page_Edit_Tabs */
        if ($tabsBlock) {
            $editBlock = $this->getLayout()->getBlock('cms_page_edit');
            /* @var $editBlock Magento_Adminhtml_Block_Cms_Page_Edit */
            if ($editBlock) {
                $page = $this->_coreRegistry->registry('cms_page');
                if ($page) {
                    if ($page->getId()) {
                        $editBlock->addButton('preview', array(
                            'label'     => __('Preview'),
                            'class'     => 'preview',
                            'data_attribute'  => array(
                                'mage-init' => array(
                                    'button' => array(
                                        'event' => 'preview',
                                        'target' => '#edit_form',
                                        'eventData' => array(
                                            'action' => $this->getUrl('*/cms_page_revision/preview'),
                                        )
                                    ),
                                ),
                            ),
                        ));
                    }

                    $formBlock = $editBlock->getChildBlock('form');
                    if ($formBlock) {
                        $formBlock->setTemplate('Magento_VersionsCms::page/edit/form.phtml');
                        if ($page->getUnderVersionControl()) {
                            $tabId = $this->getRequest()->getParam('tab');
                            if ($tabId) {
                                $formBlock->setSelectedTabId($tabsBlock->getId() . '_' . $tabId)
                                    ->setTabJsObject($tabsBlock->getJsObjectName());
                            }
                        }
                    }
                    // If user non-publisher he can save page only if it has disabled status
                    if ($page->getUnderVersionControl()) {
                        if ($page->getId() && $page->getIsActive() == Magento_Cms_Model_Page::STATUS_ENABLED) {
                            if (!$this->_cmsConfig->canCurrentUserPublishRevision()) {
                                $editBlock->removeButton('delete');
                                $editBlock->removeButton('save');
                                $editBlock->removeButton('saveandcontinue');
                            }
                        }
                    }
                }
            }
        }
        return $this;
    }
}
