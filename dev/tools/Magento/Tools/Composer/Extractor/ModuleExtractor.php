<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use \Magento\Tools\Composer\Parser\ModuleXmlParser;

/**
 * Extractor for Modules
 */
class ModuleExtractor extends  AbstractExtractor
{

    /**
     * {@inheritdoc}
     */
    public function getSubPath()
    {
        return '/app/code/Magento/';
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "magento2-module";
    }

    /**
     * {@inheritdoc}
     */
    public function getParser($filename)
    {
        return new ModuleXmlParser($filename);
    }

}