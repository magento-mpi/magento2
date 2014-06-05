<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use \Magento\Tools\Composer\Parser\ThemeXmlParser;

/**
 * Extractor for Admin Theme
 */
class AdminThemeExtractor extends  AbstractExtractor
{

    /**
     * {@inheritdoc}
     */
    public function getSubPath()
    {
        return '/app/design/adminhtml/Magento/';
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "magento2-theme-adminhtml";
    }

    /**
     * {@inheritdoc}
     */
    public function getParser($filename)
    {
        return new ThemeXmlParser($filename);
    }

}