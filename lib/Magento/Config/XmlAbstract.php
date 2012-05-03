<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  Config
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration XML-files merger
 */
abstract class Magento_Config_XmlAbstract
{
    /**
     * Data extracted from the merged configuration files
     *
     * @var array
     */
    protected $_data;

    /**
     * Instantiate with the list of files to merge
     *
     * @param array $configFiles
     * @param Zend_Cache_Core $cache
     * @throws Magento_Exception
     */
    public function __construct(array $configFiles, Zend_Cache_Core $cache = null)
    {
        if (empty($configFiles)) {
            throw new Magento_Exception('There must be at least one configuration file specified.');
        }
        $cacheId = null;
        if ($cache) {
            $cacheId = 'CONFIG_XML_' . md5(implode('|', $configFiles));
            $cachedData = $cache->load($cacheId);
            if ($cachedData !== false) {
                $this->_data = unserialize($cachedData);
                return;
            }
        }
        $this->_data = $this->_extractData($this->_merge($configFiles));
        if ($cache) {
            $cache->save(serialize($this->_data), $cacheId);
        }
    }

    /**
     * Get absolute path to the XML-schema file
     *
     * @return string
     */
    abstract public function getSchemaFile();

    /**
     * Extract configuration data from the DOM structure
     *
     * @param DOMDocument $dom
     * @return array
     */
    abstract protected function _extractData(DOMDocument $dom);

    /**
     * Merge the config XML-files
     *
     * @param array $configFiles
     * @return DOMDocument
     * @throws Magento_Exception if a non-existing or invalid XML-file passed
     */
    protected function _merge($configFiles)
    {
        $domConfig = new Magento_Config_Dom($this->_getInitialXml(), $this->_getIdAttributes());
        foreach ($configFiles as $file) {
            if (!file_exists($file)) {
                throw new Magento_Exception("File does not exist: {$file}");
            }
            $domConfig->merge(file_get_contents($file));
            if (!$domConfig->validate($this->getSchemaFile(), $errors)) {
                $message = "Invalid XML-file: {$file}\n";
                /** @var libXMLError $error */
                foreach ($errors as $error) {
                    $message .= "{$error->message} Line: {$error->line}\n";
                }
                throw new Magento_Exception($message);
            }
        }
        return $domConfig->getDom();
    }

    /**
     * Get XML-contents, initial for merging
     *
     * @return string
     */
    abstract protected function _getInitialXml();

    /**
     * Get list of paths to identifiable nodes
     *
     * @return array
     */
    abstract protected function _getIdAttributes();
}
