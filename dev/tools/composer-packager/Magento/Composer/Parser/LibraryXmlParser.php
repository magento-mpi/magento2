<?php

namespace Magento\Composer\Parser;

class LibraryXmlParser extends AbstractXmlParser {

    public function getSubPath(){
        return '/library.xml';
    }

    /**
     * @throws \ErrorException
     * @return |Magento\Composer|Model\ArrayAndObjectAccess
     */
    protected function _parseMappings()
    {
        /** @var $package |SimpleXMLElement */
        $package = simplexml_load_file($this->getFile()->getPathname());
        if (isset($package)) {
            $map = array();
            $libraryDefinitions = new \Magento\Composer\Model\ArrayAndObjectAccess();
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