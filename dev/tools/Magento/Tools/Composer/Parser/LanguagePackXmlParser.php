<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Parser;

use \Magento\Tools\Composer\Model\ArrayAndObjectAccess;

class LanguagePackXmlParser extends AbstractXmlParser
{

    public function getSubPath()
    {
        return '/language.xml';
    }

    /**
     * @throws \ErrorException
     * @return ArrayAndObjectAccess
     */
    protected function _parseMappings()
    {
        /** @var $package SimpleXMLElement */
        $package = simplexml_load_file($this->getFile()->getPathname());
        $moduleDefinitions = new ArrayAndObjectAccess();

        if (isset($package)) {
            $map = array();
            $moduleDefinitions->name = (string)$package->xpath('language/@name')[0];
            $moduleDefinitions->version = (string)$package->xpath('language/@version')[0];
            $moduleDefinitions->location = $this->getComponentDir();
            foreach ($package->xpath('language/depends/framework') as $framework) {
                $map['Magento/Framework'] = "Magento/Framework";
            }
            foreach ($package->xpath('language/depends/language/@name') as $depends) {
                    $map[(string)$depends] =  (string)$depends;
            }
            $moduleDefinitions->dependencies = $map;
        }
        return $moduleDefinitions;
    }

}