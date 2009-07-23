<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Cms page edit form revisions tab
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Adding js to CE blocks to implemnt special functionality which
     * will allow go back to edit page with preloaded tab passed through query string
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page_Edit_Tab_Versions
     */
    protected function _prepareLayout()
    {
        $tabsBlock = $this->getLayout()->getBlock('cms_page_edit_tabs');
        if ($tabsBlock) {
            $editBlock = $this->getLayout()->getBlock('cms_page_edit');
            if ($editBlock) {
                $formBlock = $editBlock->getChild('form');
                if ($formBlock) {
                    $tabId = $this->getRequest()->getParam('tab');
                    if ($tabId) {
                        $formBlock->setSelectedTabId($tabsBlock->getId() . '_' . $tabId)
                            ->setTabJsObject($tabsBlock->getJsObjectName())
                            ->setTemplate('enterprise/cms/page/edit/form.phtml');
                    }
                }
            }
        }
        return $this;
    }
}
