<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use Magento\Tools\Composer\Model\Package;

/**
 * Extractor for FrontEnd Theme
 */
class FrontendThemeExtractor extends  ExtractorAbstract
{
    /**
     * {@inheritdoc}
     */
    public function getPath()
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