<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Parser;

/**
 * XML Parser for Library Files
 */
class LibraryXmlParser extends XmlParserAbstract
{

    /**
     * {@inheritdoc}
     */
    public function getSubPath()
    {
        return '/library.xml';
    }

    /**
     * {@inheritdoc}
     */
    protected function parseMappings()
    {
        $package = simplexml_load_file($this->getFile()->getPathname());
        $definitions = array();

        if (isset($package)) {
            $map = array();
            foreach ($package->xpath('library/depends/library/@name') as $depends) {
                        $map[(string)$depends] =  (string)$depends;
            }
            $definitions = $this->createDefinition(
                (string)$package->xpath('library/@name')[0],
                (string)$package->xpath('library/@version')[0],
                $this->getComponentDir(),
                $map
            );
        }
        return $definitions;
    }

}