<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Parser;

use \Magento\Tools\Composer\Model\ArrayAndObjectAccess;

class LibraryXmlParser extends AbstractXmlParser
{

    public function getSubPath()
    {
        return '/library.xml';
    }

    /**
     * @throws \ErrorException
     * @return ArrayAndObjectAccess
     */
    protected function _parseMappings()
    {
        /** @var $package |SimpleXMLElement */
        $package = simplexml_load_file($this->getFile()->getPathname());
        $libraryDefinitions = new ArrayAndObjectAccess();

        if (isset($package)) {
            $map = array();
            $libraryDefinitions->name = (string)$package->xpath('library/@name')[0];
            $libraryDefinitions->version = (string)$package->xpath('library/@version')[0];
            $libraryDefinitions->location = $this->getComponentDir();
            foreach ($package->xpath('library/depends/library/@name') as $depends) {
                        $map[(string)$depends] =  (string)$depends;
            }
            $libraryDefinitions->dependencies = $map;
        }
        return $libraryDefinitions;
    }

}