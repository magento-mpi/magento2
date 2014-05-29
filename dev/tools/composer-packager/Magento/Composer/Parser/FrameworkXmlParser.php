<?php

namespace Magento\Composer\Parser;

class FrameworkXmlParser implements \Magento\Composer\Parser {

    private $_frameworkDir = null;

    protected $_file = null;

    public function __construct($frameworkDir)
    {
        $this->setframeworkDir($frameworkDir);
        $this->setFile($frameworkDir.'/library.xml');
    }

    public function setFrameworkDir($frameworkDir){
        // Remove trailing slash
        if (!is_null($frameworkDir)) {
            $frameworkDir = rtrim($frameworkDir, '\\/');
        }
        $this->_frameworkDir = $frameworkDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getFrameworkDir()
    {
        return $this->_frameworkDir;
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
            $frameworkDefinitions = new \Magento\Composer\Model\ArrayAndObjectAccess();
            $frameworkDefinitions->name = (string)$package->xpath('library/@name')[0];
            $frameworkDefinitions->version = (string)$package->xpath('library/@version')[0];
            $frameworkDefinitions->location = $this->getFrameworkDir();
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
            $frameworkDefinitions->dependencies = $map;
        }
        return $frameworkDefinitions;
    }

}