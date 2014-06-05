<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer\Parser;

use \Magento\Tools\Composer\ParserInterface;

abstract class AbstractXmlParser implements ParserInterface
{

    protected $_componentDir;
    protected $_file;

    public function __construct($componentDir)
    {
        $this->_componentDir = $componentDir;
        $this->setFile($this->_componentDir.$this->getSubPath());
    }

    public abstract function getSubPath();

    protected abstract function _parseMappings();

    /**
     * @return string
     */
    public function getComponentDir()
    {
        return $this->_componentDir ;
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
            throw new \ErrorException(sprintf('Component file "%s" not readable', $file->getPathname()));
        }

        $map = $this->_parseMappings();
        return $map;
    }


}