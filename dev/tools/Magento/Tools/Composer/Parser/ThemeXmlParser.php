<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Parser;

/**
 * XML Parser for Themes
 */
class ThemeXmlParser extends AbstractXmlParser
{

    /**
     * {@inheritdoc}
     */
    public function getSubPath()
    {
        return '/theme.xml';
    }

    /**
     * {@inheritdoc}
     */
    protected function _parseMappings()
    {
        /** @var $package \SimpleXMLElement */
        $package = simplexml_load_file($this->getFile()->getPathname());

        $themeDefinitions = array();
        if (isset($package)) {
            $map = array();
            $name = (string)$package->xpath('name')[0];
            //Dependencies
            $dependency = $package->xpath("parent");

            if (!empty($dependency)) {
                $depName = (String)$dependency[0] . "-Theme";
                $map[$depName] = $depName;
            }
            $themeDefinitions = $this->createDefinition(
                (string)$name . "-Theme",
                (string)$package->xpath('version')[0],
                $this->getComponentDir(),
                $map
            );
        }

        return $themeDefinitions;
    }

}