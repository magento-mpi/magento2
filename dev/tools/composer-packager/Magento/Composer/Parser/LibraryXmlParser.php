<?php

namespace Magento\Composer\Parser;

class LibraryXmlParser implements \Magento\Composer\Parser {

    private $_libraryDir = null;

    protected $_file = null;

    public function __construct($libraryDir)
    {
        $this->_libraryDir= $libraryDir;
        $this->setFile($libraryDir.'/library.xml');
    }

    /**
     * @return string
     */
    public function getLibraryDir()
    {
        return $this->_libraryDir;
    }

    /**
     * @param string|SplFileObject $file
     * @return PackageXmlParser
     */
    public function setFile($file)
    {
        if (is_string($file)) {
            $file = new \SplFileObject($file);
        }
        $this->_file = $file;
        return $this;
    }

    /**
     * @return \SplFileObject
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * @return array
     * @throws \ErrorException
     */
    public function getMappings()
    {
        $file = $this->getFile();

        if (!$file->isReadable()) {
            throw new \ErrorException(sprintf('Package file "%s" not readable', $file->getPathname()));
        }

        $map = $this->_parseMappings();
        return $map;
    }

    /**
     * @throws \ErrorException
     * @return Magento\Composer\ArrayAndObjectAccess
     */
    protected function _parseMappings()
    {
        $map = array();

        /** @var $package SimpleXMLElement */
        $package = simplexml_load_file($this->getFile()->getPathname());

        if (isset($package)) {
            $map = array();
            $libraryDefinitions = new \Magento\Composer\Model\ArrayAndObjectAccess();
            $libraryDefinitions->name = (string)$package->xpath('library/@name')[0];
            $libraryDefinitions->version = (string)$package->xpath('library/@version')[0];
            $libraryDefinitions->location = $this->getLibraryDir();
            foreach ($package->xpath('library/depends/library/@name') as $depends) {
                try {
                        $map[(string)$depends] =  (string)$depends;
                }
                catch (RuntimeException $e) {
                    // Skip invalid targets
                    throw $e;
                    continue;
                }
            }
            $libraryDefinitions->dependencies = $map;
        }
        return $libraryDefinitions;
    }

}