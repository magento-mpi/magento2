<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Parser;

use \Magento\Tools\Composer\Model\ArrayAndObjectAccess;

class ModuleXmlParser extends AbstractXmlParser
{

    public function getSubPath()
    {
        return '/etc/module.xml';
    }

    /**
     * @throws \ErrorException
     * @return ArrayAndObjectAccess
     */
    protected function _parseMappings()
    {
        /** @var $package \SimpleXMLElement */
        $package = simplexml_load_file($this->getFile()->getPathname());

        $moduleDefinitions = new ArrayAndObjectAccess();
        if (isset($package)) {
            $map = array();
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