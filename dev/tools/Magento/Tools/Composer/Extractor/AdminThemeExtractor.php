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
 * Extractor for Admin Theme
 */
class AdminThemeExtractor extends ExtractorAbstract
{

    /**
     * {@inheritdoc}
     */
    public function getPath()
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
}