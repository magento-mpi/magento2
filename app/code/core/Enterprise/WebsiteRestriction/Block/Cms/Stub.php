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
 * @package    Enterprise_WebsiteRestriction
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Stub block that outputs a raw CMS-page
 *
 */
class Enterprise_WebsiteRestriction_Block_Cms_Stub extends Mage_Core_Block_Abstract
{
    /**
     * Set page identifier and load the page model at once
     *
     * @param string $pageIdentifier
     * @return Enterprise_WebsiteRestriction_Block_Cms_Page
     */
    public function setPageIdentifier($pageIdentifier)
    {
        $page = Mage::getModel('cms/page')->load($pageIdentifier, 'identifier');
        $this->setData('page_identifier', $pageIdentifier)->setData('page_model', $page);
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle($page->getTitle());
            $head->setKeywords($page->getMetaKeywords());
            $head->setDescription($page->getMetaDescription());
        }
        return $this;
    }

    /**
     * Render the CMS page raw content
     *
     * @return string
     */
    protected function _toHtml()
    {
        $html = $this->getPageModel()->getContent();
        $html = $this->getMessagesBlock()->getGroupedHtml() . $html;
        return $html;
    }
}
