<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Plugin;

class CmsPage
{
    /**
     * Add custom CMS page statuses
     *
     * @param array $invocationResult
     * @return array
     */
    public function afterGetAvailableStatuses(\Magento\Cms\Model\Page $subject, array $invocationResult)
    {
        $invocationResult[\Magento\Cms\Model\Page::STATUS_ENABLED] = __('Published');
        return $invocationResult;
    }
}
