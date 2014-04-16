<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback;

/**
 * Provider of template view files
 */
class TemplateFile extends File
{
    /**
     * @return string
     */
    protected function getFallbackType()
    {
        return \Magento\View\Design\Fallback\RulePool::TYPE_TEMPLATE_FILE;
    }
}
