<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_VersionsCms_Model_Plugin_CmsPage
{
    /**
     * Add custom CMS page statuses
     *
     * @param array $invocationResult
     * @return array
     */
    public function afterGetAvailableStatuses(array $invocationResult)
    {
        $invocationResult[Magento_Cms_Model_Page::STATUS_ENABLED] = __('Published');
        return $invocationResult;
    }
}
