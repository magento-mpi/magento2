<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Parser;

/**
 * Xml Parser for Language Packs
 */
class LanguagePackXmlParser extends XmlParserAbstract
{

    /**
     * {@inheritdoc}
     */
    public function getSubPath()
    {
        return '/language.xml';
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
            foreach ($package->xpath('use') as $depends) {
                $map[(string)$depends] =  (string)$depends;
            }
            $definitions = $this->createDefinition(
                (string)$package->xpath('/language/vendor')[0] . '_' . (string)$package->xpath('/language/code')[0],
                '0.1.0',
                $this->getComponentDir(),
                $map
            );
        }
        return $definitions;
    }

}