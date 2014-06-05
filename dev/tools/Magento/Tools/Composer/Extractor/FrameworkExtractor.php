<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Extractor;

use \Magento\Tools\Composer\Parser\LibraryXmlParser;

/**
 * Extractor for Framework
 */
class FrameworkExtractor extends  AbstractExtractor
{

    /**
     * {@inheritdoc}
     */
    public function getSubPath()
    {
        return "/lib/Magento/";
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return "magento2-framework";
    }

    /**
     * {@inheritdoc}
     */
    public function getParser($filename)
    {
        return new LibraryXmlParser($filename);
    }

}