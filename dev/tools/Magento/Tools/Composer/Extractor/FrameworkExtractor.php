<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use \Magento\Tools\Composer\Parser\LibraryXmlParser;

class FrameworkExtractor extends  AbstractExtractor
{

    public function getSubPath()
    {
        return "/lib/Magento/";
    }

    public function getType()
    {
        return "magento2-framework";
    }

    public function getParser($filename)
    {
        return new LibraryXmlParser($filename);
    }

}