<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Parser;

/**
 * Xml Parser for Nothing
 */
class NullParser extends XmlParserAbstract
{

    /**
     * {@inheritdoc}
     */
    public function getSubPath()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    protected function parseMappings()
    {
        return;
    }

}