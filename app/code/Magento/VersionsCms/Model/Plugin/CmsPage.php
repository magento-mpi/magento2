<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Model\Plugin;

class CmsPage
{
    /**
     * Add custom CMS page statuses
     *
     * @param \Magento\Cms\Model\Page $subject
     * @param string[] $invocationResult
     *
     * @return string[]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetAvailableStatuses(\Magento\Cms\Model\Page $subject, $invocationResult)
    {
        $invocationResult[\Magento\Cms\Model\Page::STATUS_ENABLED] = __('Published');
        return $invocationResult;
    }
}
