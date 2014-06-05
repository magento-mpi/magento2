<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use \Magento\Tools\Composer\Parser\ModuleXmlParser;

class ModuleExtractor extends  AbstractExtractor
{

    public function getSubPath()
    {
        return '/app/code/Magento/';
    }

    public function getType()
    {
        return "magento2-module";
    }

    public function getParser($filename)
    {
        return new ModuleXmlParser($filename);
    }

}