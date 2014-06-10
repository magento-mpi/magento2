<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

/**
 * Extractor for FrontEnd Theme
 */
class FrontendThemeExtractor extends AdminThemeExtractor
{
    /**
     * {@inheritdoc}
     */
    public function getSubPath()
    {
        return '/app/design/frontend/Magento/';
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "magento2-theme-frontend";
    }
}
