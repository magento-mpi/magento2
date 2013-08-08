<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @method Mage_Cms_Model_Resource_Page_Urlrewrite getResource() getResource()
 * @method int getCmsPageId() getCmsPageId()
 * @method int getUrlRewriteId() getUrlRewriteId()
 * @method Mage_Cms_Model_Page_Urlrewrite setCmsPageId() setCmsPageId(int)
 * @method Mage_Cms_Model_Page_Urlrewrite setUrlRewriteId() setUrlRewriteId(int)
 */
class Mage_Cms_Model_Page_Urlrewrite extends Magento_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Cms_Model_Resource_Page_Urlrewrite');
    }

    /**
     * Generate id path
     *
     * @param Mage_Cms_Model_Page $cmsPage
     * @return string
     */
    public function generateIdPath($cmsPage)
    {
        return 'cms_page/' . $cmsPage->getId();
    }

    /**
     * Generate target path
     *
     * @param Mage_Cms_Model_Page $cmsPage
     * @return string
     */
    public function generateTargetPath($cmsPage)
    {
        return 'cms/page/view/page_id/' . $cmsPage->getId();
    }

    /**
     * Get request path
     *
     * @param Mage_Cms_Model_Page $cmsPage
     * @return string
     */
    public function generateRequestPath($cmsPage)
    {
        return $cmsPage->getIdentifier();
    }
}
