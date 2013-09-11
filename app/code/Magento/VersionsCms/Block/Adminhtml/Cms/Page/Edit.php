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
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page;

class Edit
    extends \Magento\Adminhtml\Block\Template
{
    /**
     * Adding js to CE blocks to implement special functionality which
     * will allow go back to edit page with pre loaded tab passed through query string.
     * Added permission checking to remove some buttons if needed.
     *
     * @return \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Edit
     */
    protected function _prepareLayout()
    {
        $tabsBlock = $this->getLayout()->getBlock('cms_page_edit_tabs');
        /* @var $tabBlock \Magento\Adminhtml\Block\Cms\Page\Edit\Tabs */
        if ($tabsBlock) {
            $editBlock = $this->getLayout()->getBlock('cms_page_edit');
            /* @var $editBlock \Magento\Adminhtml\Block\Cms\Page\Edit */
            if ($editBlock) {
                $page = \Mage::registry('cms_page');
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
                        if ($page->getId() && $page->getIsActive() == \Magento\Cms\Model\Page::STATUS_ENABLED) {
                            if (!\Mage::getSingleton('Magento\VersionsCms\Model\Config')->canCurrentUserPublishRevision()) {
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
