<?php

namespace Magento\Composer\Parser;

class ThemeXmlParser implements \Magento\Composer\Parser {

    private $_themeDir = null;

    protected $_file = null;

    public function __construct($themeDir)
    {
        $this->setThemeDir($themeDir);
        $this->setFile($themeDir.'/theme.xml');
    }

    public function setThemeDir($themeDir){
        // Remove trailing slash
        if (!is_null($themeDir)) {
            $themeDir = rtrim($themeDir, '\\/');
        }
        $this->_themeDir = $themeDir;
        return $this;
    }

    /**
     * @return string
     */
    public function getThemeDir()
    {
        return $this->_themeDir;
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
        $path = $this->getFile()->getPathname();
        /** @var $package SimpleXMLElement */
        $package = simplexml_load_file($this->getFile()->getPathname());

        if (isset($package)) {
            $map = array();
            $themeDefinitions = new \Magento\Composer\Model\ArrayAndObjectAccess();
            $name = (string)$package->xpath('name')[0];
            $themeDefinitions->name = (string)$name . "-Theme";
            $themeDefinitions->version = (string)$package->xpath('version')[0];
            $themeDefinitions->location = $this->getThemeDir();
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