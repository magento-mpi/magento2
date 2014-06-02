<?php

namespace Magento\Composer\Parser;

class ThemeXmlParser extends AbstractXmlParser {

    public function getSubPath(){
        return '/theme.xml';
    }

    /**
     * @throws \ErrorException
     * @return \Magento\Composer\Model\ArrayAndObjectAccess
     */
    protected function _parseMappings()
    {
        $path = $this->getFile()->getPathname();
        /** @var $package \SimpleXMLElement */
        $package = simplexml_load_file($this->getFile()->getPathname());

        if (isset($package)) {
            $map = array();
            $themeDefinitions = new \Magento\Composer\Model\ArrayAndObjectAccess();
            $name = (string)$package->xpath('name')[0];
            $themeDefinitions->name = (string)$name . "-Theme";
            $themeDefinitions->version = (string)$package->xpath('version')[0];
            $themeDefinitions->location = $this->getComponentDir();
            //Dependencies
            $dependency = $package->xpath("parent");

            if(!empty($dependency)){
               $depName = (String)$dependency[0] . "-Theme";
               $map[$depName] = $depName;
               $themeDefinitions->dependencies = $map;
            }
        }

        return $themeDefinitions;
    }

}