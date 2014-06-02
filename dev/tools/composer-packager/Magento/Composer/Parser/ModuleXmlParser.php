<?php

namespace Magento\Composer\Parser;

class ModuleXmlParser extends AbstractXmlParser {

    public function getSubPath(){
        return '/etc/module.xml';
    }

    /**
     * @throws \ErrorException
     * @return Magento\Composer\ArrayAndObjectAccess
     */
    protected function _parseMappings()
    {
        /** @var $package SimpleXMLElement */
        $package = simplexml_load_file($this->getFile()->getPathname());
        if (isset($package)) {
            $map = array();
            $moduleDefinitions = new \Magento\Composer\Model\ArrayAndObjectAccess();
            $moduleDefinitions->name = (string)$package->xpath('module/@name')[0];
            $moduleDefinitions->version = (string)$package->xpath('module/@version')[0];
            $moduleDefinitions->location = $this->getComponentDir();
            foreach ($package->xpath('module/depends/module/@name') as $depends) {
                    $map[(string)$depends] =  (string)$depends;
            }
            $moduleDefinitions->dependencies = $map;
        }
        return $moduleDefinitions;
    }

}