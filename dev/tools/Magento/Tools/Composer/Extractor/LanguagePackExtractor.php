<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use \Magento\Tools\Composer\Parser\LanguagePackXmlParser;

class LanguagePackExtractor extends  AbstractExtractor
{

    public function getSubPath()
    {
        return '/app/i18n/Magento/';
    }

    public function getType()
    {
        return "magento2-language";
    }

    public function getParser($filename)
    {
        return new LanguagePackXmlParser($filename);
    }

}