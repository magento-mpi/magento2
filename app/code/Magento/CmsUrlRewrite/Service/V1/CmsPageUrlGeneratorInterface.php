<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CmsUrlRewrite\Service\V1;

/**
 * Product Generator
 */
interface CmsPageUrlGeneratorInterface
{
    /**
     * Generate list of urls
     * TODO: fix service parameter
     *
     * @param \Magento\Cms\Model\Page $cmsPage
     * @return \Magento\UrlRedirect\Service\V1\Data\UrlRewrite[]
     */
    public function generate($cmsPage);
}
