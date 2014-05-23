<?php

namespace Magento\Composer\Parser;

class PackageXmlParser implements \Magento\Composer\Parser {

    private $_moduleDir = null;

    protected $_file = null;

    public function __construct($moduleDir)
    {
        $this->setModuleDir($moduleDir);
        $this->setFile($moduleDir.'/etc/module.xml');
    }

    public function setModuleDir($moduleDir){
        // Remove trailing slash
        if (!is_null($moduleDir)) {
            $moduleDir = rtrim($moduleDir, '\\/');
        }
        $this->_moduleDir = $moduleDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getModuleDir()
    {
        return $this->_moduleDir;
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

        //$json = json_encode($package);
        if (isset($package)) {
           // echo "\n\n\n";
            $map = array();
            $moduleDefinitions = new \Magento\Composer\Model\ArrayAndObjectAccess();
            $moduleDefinitions->name = (string)$package->xpath('module/@name')[0];
            $moduleDefinitions->version = (string)$package->xpath('module/@version')[0];
            $moduleDefinitions->active = (bool)$package->xpath('module/@active')[0];
            $moduleDefinitions->location = $this->getModuleDir();
            foreach ($package->xpath('module/depends/module/@name') as $depends) {
                try {
             //       echo $depends, "\n";
                    $map[(string)$depends] =  (string)$depends;
                    //Get all the details and create the module object
                }
                catch (RuntimeException $e) {
                    // Skip invalid targets
                    throw $e;
                    continue;
                }
            }
            $moduleDefinitions->dependencies = $map;
        }
        return $moduleDefinitions;
    }

}