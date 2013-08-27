<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Stub block that outputs a raw CMS-page
 *
 */
class Enterprise_WebsiteRestriction_Block_Cms_Stub extends Magento_Cms_Block_Page
{
    /**
     * Retrieve page from registry if it is not there try to laod it by indetifier
     *
     * @return Magento_Cms_Model_Page
     */

    public function getPage()
    {
        if (!$this->hasData('page')) {
            $page = Mage::registry('restriction_landing_page');
            if (!$page) {
                $page = Mage::getModel('Magento_Cms_Model_Page')
                    ->load($this->getPageIdentifier(), 'identifier');
            }
            $this->setData('page', $page);
        }
        return $this->getData('page');
    }

    protected function _prepareLayout()
    {
        $page = $this->getPage();

        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('cms-'.$page->getIdentifier());
        }

        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setTitle($page->getTitle());
            $head->setKeywords($page->getMetaKeywords());
            $head->setDescription($page->getMetaDescription());
        }
    }
}
