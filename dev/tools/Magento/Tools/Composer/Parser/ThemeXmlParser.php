<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Parser;

use \Magento\Tools\Composer\Model\ArrayAndObjectAccess;

class ThemeXmlParser extends AbstractXmlParser
{

    public function getSubPath()
    {
        return '/theme.xml';
    }

    /**
     * @throws \ErrorException
     * @return ArrayAndObjectAccess
     */
    protected function _parseMappings()
    {
        /** @var $package \SimpleXMLElement */
        $package = simplexml_load_file($this->getFile()->getPathname());

        $themeDefinitions = new ArrayAndObjectAccess();
        if (isset($package)) {
            $map = array();
            $name = (string)$package->xpath('name')[0];
            $themeDefinitions->name = (string)$name . "-Theme";
            $themeDefinitions->version = (string)$package->xpath('version')[0];
            $themeDefinitions->location = $this->getComponentDir();
            //Dependencies
            $dependency = $package->xpath("parent");

            if (!empty($dependency)) {
                $depName = (String)$dependency[0] . "-Theme";
                $map[$depName] = $depName;
                $themeDefinitions->dependencies = $map;
            }
        }

        return $themeDefinitions;
    }

}