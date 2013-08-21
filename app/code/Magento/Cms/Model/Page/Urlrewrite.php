<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @method Magento_Cms_Model_Resource_Page_Urlrewrite getResource() getResource()
 * @method int getCmsPageId() getCmsPageId()
 * @method int getUrlRewriteId() getUrlRewriteId()
 * @method Magento_Cms_Model_Page_Urlrewrite setCmsPageId() setCmsPageId(int)
 * @method Magento_Cms_Model_Page_Urlrewrite setUrlRewriteId() setUrlRewriteId(int)
 */
class Magento_Cms_Model_Page_Urlrewrite extends Magento_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Cms_Model_Resource_Page_Urlrewrite');
    }

    /**
     * Generate id path
     *
     * @param Magento_Cms_Model_Page $cmsPage
     * @return string
     */
    public function generateIdPath($cmsPage)
    {
        return 'cms_page/' . $cmsPage->getId();
    }

    /**
     * Generate target path
     *
     * @param Magento_Cms_Model_Page $cmsPage
     * @return string
     */
    public function generateTargetPath($cmsPage)
    {
        return 'cms/page/view/page_id/' . $cmsPage->getId();
    }

    /**
     * Get request path
     *
     * @param Magento_Cms_Model_Page $cmsPage
     * @return string
     */
    public function generateRequestPath($cmsPage)
    {
        return $cmsPage->getIdentifier();
    }
}
