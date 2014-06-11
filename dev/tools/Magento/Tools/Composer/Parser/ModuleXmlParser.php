<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Parser;

/**
 * Xml Parser for Modules
 */
class ModuleXmlParser extends XmlParserAbstract
{

    /**
     * {@inheritdoc}
     */
    public function getSubPath()
    {
        return '/etc/module.xml';
    }

    /**
     * {@inheritdoc}
     */
    protected function parseMappings()
    {
        /** @var $package \SimpleXMLElement */
        $package = simplexml_load_file($this->getFile()->getPathname());

        $definitions = array();

        if (isset($package)) {
            $map = array();
            foreach ($package->xpath('module/depends/module/@name') as $depends) {
                    $map[(string)$depends] =  (string)$depends;
            }
            $definitions = $this->createDefinition(
                (string)$package->xpath('module/@name')[0],
                (string)$package->xpath('module/@version')[0],
                $this->getComponentDir(),
                $map
            );
        }
        return $definitions;
    }
}
