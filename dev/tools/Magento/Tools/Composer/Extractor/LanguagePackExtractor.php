<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use \Magento\Tools\Composer\Parser\LanguagePackXmlParser;

/**
 * Extractor for Language Pack
 */
class LanguagePackExtractor extends AbstractExtractor
{

    /**
     * {@inheritdoc}
     */
    public function getSubPath()
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

    /**
     * {@inheritdoc}
     */
    public function getParser($filename)
    {
        return new LanguagePackXmlParser($filename);
    }
}
