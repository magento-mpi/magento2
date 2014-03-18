<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Model\Page;

/**
 * Page Url rewrite model
 *
 * @method \Magento\Cms\Model\Resource\Page\Urlrewrite getResource() getResource()
 * @method int getCmsPageId() getCmsPageId()
 * @method int getUrlRewriteId() getUrlRewriteId()
 * @method \Magento\Cms\Model\Page\Urlrewrite setCmsPageId() setCmsPageId(int)
 * @method \Magento\Cms\Model\Page\Urlrewrite setUrlRewriteId() setUrlRewriteId(int)
 */
class Urlrewrite extends \Magento\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Cms\Model\Resource\Page\Urlrewrite');
    }

    /**
     * Generate id path
     *
     * @param \Magento\Cms\Model\Page $cmsPage
     * @return string
     */
    public function generateIdPath($cmsPage)
    {
        return 'cms_page/' . $cmsPage->getId();
    }

    /**
     * Generate target path
     *
     * @param \Magento\Cms\Model\Page $cmsPage
     * @return string
     */
    public function generateTargetPath($cmsPage)
    {
        return 'cms/page/view/page_id/' . $cmsPage->getId();
    }

    /**
     * Get request path
     *
     * @param \Magento\Cms\Model\Page $cmsPage
     * @return string
     */
    public function generateRequestPath($cmsPage)
    {
        return $cmsPage->getIdentifier();
    }
}
