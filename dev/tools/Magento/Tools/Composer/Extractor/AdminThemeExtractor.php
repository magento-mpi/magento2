<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use \Magento\Tools\Composer\Parser\ThemeXmlParser;

class AdminThemeExtractor extends  AbstractExtractor
{

    public function getSubPath()
    {
        return '/app/design/adminhtml/Magento/';
    }

    public function getType()
    {
        return "magento2-theme-adminhtml";
    }

    public function getParser($filename)
    {
        return new ThemeXmlParser($filename);
    }

}