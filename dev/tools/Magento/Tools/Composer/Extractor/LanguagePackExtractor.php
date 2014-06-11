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
 * Extractor for Language Pack
 */
class LanguagePackExtractor extends  ExtractorAbstract
{

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return '/app/i18n/Magento/';
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "magento2-language";
    }

}