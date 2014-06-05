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
class LanguagePackXmlParser extends AbstractXmlParser
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
    protected function _parseMappings()
    {
        $package = simplexml_load_file($this->getFile()->getPathname());
        $moduleDefinitions = array();

        if (isset($package)) {
            $map = array();
            foreach ($package->xpath('language/depends/framework') as $framework) {
                $map['Magento/Framework'] = "Magento/Framework";
            }
            foreach ($package->xpath('language/depends/language/@name') as $depends) {
                $map[(string)$depends] =  (string)$depends;
            }
            $moduleDefinitions = $this->createDefinition(
                (string)$package->xpath('language/@name')[0],
                (string)$package->xpath('language/@version')[0],
                $this->getComponentDir(),
                $map
            );
        }
        return $moduleDefinitions;
    }

}