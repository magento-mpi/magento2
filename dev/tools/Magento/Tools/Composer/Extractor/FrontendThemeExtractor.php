<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

class FrontendThemeExtractor extends  AdminThemeExtractor
{

    public function getSubPath()
    {
        return '/app/design/frontend/Magento/';
    }

    public function getType()
    {
        return "magento2-theme-frontend";
    }

}