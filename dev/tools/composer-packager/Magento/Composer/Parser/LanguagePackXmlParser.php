<?php

namespace Magento\Composer\Parser;

class LanguagePackXmlParser implements \Magento\Composer\Parser {

    private $_lpDir = null;

    protected $_file = null;

    public function __construct($lpDir)
    {
        $this->_lpDir = $lpDir;
        $this->setFile($lpDir.'/language.xml');
    }

    /**
     * @return string
     */
    public function getLanguagePackDir()
    {
        return $this->_lpDir;
    }

    /**
     * @param string|SplFileObject $file
     * @return PackageXmlParser
     */
    protected function setFile($file)
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
            $moduleDefinitions->name = (string)$package->xpath('language/@name')[0];
            $moduleDefinitions->version = (string)$package->xpath('language/@version')[0];
            $moduleDefinitions->location = $this->getLanguagePackDir();
            foreach($package->xpath('language/depends/framework') as $framework){
                $map['Magento/Framework'] = "Magento/Framework";
            }
            foreach ($package->xpath('language/depends/language/@name') as $depends) {
                try {
                    $map[(string)$depends] =  (string)$depends;
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